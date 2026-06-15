<?php

namespace App\Services;

use App\Models\ProjectElementCost;
use App\Models\RegionalRate;
use Illuminate\Support\Facades\DB;

class ForecastService
{
    /**
     * Bayesian-style shrinkage blend ("blend toward the broad mean").
     *
     * A local rate based on only a handful of comparable projects is
     * unreliable, so it is pulled toward a broader mean. How hard it is
     * pulled depends on n (how much local evidence backs it) versus k
     * (the shrinkage strength, expressed as an equivalent number of
     * "broad mean" projects of evidence):
     *
     *   blended = (n * localRate + k * broadMean) / (n + k)
     *
     * - n large relative to k  -> blended ≈ localRate (local data dominates)
     * - n small relative to k  -> blended is pulled strongly toward broadMean
     *
     * This is fully deterministic and reproducible from the inputs alone.
     */
    public function blendRate(float $localRate, int $n, float $broadMean, float $k): float
    {
        if (($n + $k) <= 0) {
            return $localRate;
        }

        return (($n * $localRate) + ($k * $broadMean)) / ($n + $k);
    }

    /**
     * The mean rate_per_m2 for an estimating element, pooled across ALL
     * regions (historical projects only, excluding any flagged
     * exclude_from_estimator). This is the "broad mean" that sparse local
     * rates are blended toward.
     */
    public function broadMeanForElement(int $estimatingElementId): float
    {
        return (float) ProjectElementCost::whereHas('project', function ($query) {
                $query->where('project_type', 'historical')
                    ->where('exclude_from_estimator', false);
            })
            ->where('estimating_element_id', $estimatingElementId)
            ->avg('rate_per_m2') ?? 0.0;
    }

    /**
     * Blend all four bands (Low/Medium/High/High+) of a RegionalRate row
     * toward the broad mean for that element, using the same n and k for
     * each band. High+ is recalculated from the blended High rate
     * (blended High x 1.15), matching how High+ is derived from raw data.
     *
     * Returns the blended rates alongside the raw pre-blend rates and the
     * inputs used, so the report can show a transparent audit trail.
     */
    public function blendRegionalRate(RegionalRate $rate, float $k): array
    {
        $n = (int) $rate->project_count;
        $broadMean = $this->broadMeanForElement($rate->estimating_element_id);

        $blendedLow = $this->blendRate((float) $rate->low_rate, $n, $broadMean, $k);
        $blendedMedium = $this->blendRate((float) $rate->medium_rate, $n, $broadMean, $k);
        $blendedHigh = $this->blendRate((float) $rate->high_rate, $n, $broadMean, $k);
        $blendedHighPlus = $blendedHigh * 1.15;

        return [
            'low' => $blendedLow,
            'medium' => $blendedMedium,
            'high' => $blendedHigh,
            'high_plus' => $blendedHighPlus,
            'n' => $n,
            'k' => $k,
            'broad_mean' => $broadMean,
            'pre_blend' => [
                'low' => (float) $rate->low_rate,
                'medium' => (float) $rate->medium_rate,
                'high' => (float) $rate->high_rate,
                'high_plus' => (float) $rate->high_plus_rate,
            ],
            // Only flag as "blended" when the central rate moved by more
            // than a trivial rounding amount, so the badge doesn't appear
            // for elements where n is already large enough that the blend
            // has no real effect.
            'was_blended' => abs($blendedMedium - (float) $rate->medium_rate) > 0.5,
        ];
    }

    /**
     * Sample standard deviation (n-1 denominator) of an array of values.
     * Returns 0.0 if fewer than 2 values — variance cannot be estimated
     * from a single data point.
     */
    public function standardDeviation(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }

        $mean = array_sum($values) / $n;
        $variance = array_sum(array_map(fn($v) => ($v - $mean) ** 2, $values)) / ($n - 1);

        return sqrt($variance);
    }

    /**
     * Stage 2 uncertainty multiplier. The forecast band's half-width
     * scales with 1/sqrt(n): fewer comparable projects -> wider band.
     *
     *   multiplier = 1 + (C / sqrt(n))
     *
     * n is floored at 1 to avoid division by zero.
     */
    public function uncertaintyMultiplier(int $n, float $c): float
    {
        $n = max($n, 1);

        return 1 + ($c / sqrt($n));
    }

    /**
     * Widen a Low/High band around a fixed Mid using base_spread (already
     * expressed in the same units as $mid) and the uncertainty multiplier
     * for n comparable projects. Mid itself is unchanged. Low is floored
     * at zero (a forecast can never be negative).
     */
    public function widenBand(float $mid, float $baseSpread, int $n, float $c): array
    {
        $multiplier = $this->uncertaintyMultiplier($n, $c);
        $halfWidth = $baseSpread * $multiplier;

        return [
            'low' => max(0, $mid - $halfWidth),
            'high' => $mid + $halfWidth,
            'multiplier' => $multiplier,
            'base_spread' => $baseSpread,
            'n' => $n,
            'c' => $c,
        ];
    }

    /**
     * Per-project total rate (PKR/m2, summed across all estimating
     * elements) for historical, non-excluded comparable projects in a
     * region. This is the "comparable rates" array the area-based
     * base_spread is computed from.
     */
    public function areaRatesForRegion(int $regionId): array
    {
        return DB::table('project_element_costs')
            ->join('projects', 'projects.id', '=', 'project_element_costs.project_id')
            ->where('projects.project_type', 'historical')
            ->where('projects.region_id', $regionId)
            ->where('projects.exclude_from_estimator', false)
            ->groupBy('projects.id')
            ->select(DB::raw('SUM(project_element_costs.rate_per_m2) as total_rate'))
            ->pluck('total_rate')
            ->map(fn($v) => (float) $v)
            ->toArray();
    }

    /**
     * Same as areaRatesForRegion() but pooled across ALL regions. Used as
     * the base_spread fallback when a region has fewer than 2 comparables.
     */
    public function broadAreaRates(): array
    {
        return DB::table('project_element_costs')
            ->join('projects', 'projects.id', '=', 'project_element_costs.project_id')
            ->where('projects.project_type', 'historical')
            ->where('projects.exclude_from_estimator', false)
            ->groupBy('projects.id')
            ->select(DB::raw('SUM(project_element_costs.rate_per_m2) as total_rate'))
            ->pluck('total_rate')
            ->map(fn($v) => (float) $v)
            ->toArray();
    }

    /**
     * Cost-per-seat for every historical project with seating data, pooled
     * across ALL regions. Used as the base_spread fallback when a region
     * has fewer than 2 seat-based comparables.
     */
    public function broadSeatRates(): array
    {
        return DB::table('projects')
            ->join('transactions', 'projects.id', '=', 'transactions.project_id')
            ->where('projects.project_type', 'historical')
            ->whereNotNull('projects.seating_capacity')
            ->where('projects.seating_capacity', '>', 0)
            ->groupBy('projects.id', 'projects.seating_capacity')
            ->select('projects.seating_capacity', DB::raw('SUM(transactions.amount) as total_cost'))
            ->get()
            ->map(fn($row) => (float) $row->total_cost / (float) $row->seating_capacity)
            ->toArray();
    }
}
