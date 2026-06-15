<?php

namespace App\Http\Controllers;

use App\Services\AiAdvisoryService;
use Illuminate\Support\Facades\DB;

/**
 * Stage 3 advisory AI endpoints. These never compute or alter any cost
 * figure — they hand the already-computed figures from
 * ProjectReportController::buildReportData() to AiAdvisoryService, which
 * returns selections/explanations/flags only.
 */
class AiAdvisoryController extends Controller
{
    public function __construct(
        private ProjectReportController $reportController,
        private AiAdvisoryService $ai,
    ) {
    }

    public function summary($projectId)
    {
        $data = $this->reportController->buildReportData($projectId);

        $context = $this->summarize($data);
        $context['risk_signals'] = $this->riskSignals($data);

        return response()->json(
            $this->ai->executiveSummary($context)
        );
    }

    public function similarProjects($projectId)
    {
        $data = $this->reportController->buildReportData($projectId);
        $project = $data['project'];

        $projectProfile = [
            'name' => $project->name,
            'region' => $project->region?->name,
            'gross_floor_area_m2' => (float) ($project->gross_floor_area ?? 0),
            'seating_capacity' => $project->seating_capacity,
        ];

        $candidates = $this->candidateProjects((int) $projectId);

        return response()->json(
            $this->ai->findSimilarProjects($projectProfile, $candidates)
        );
    }

    public function explain($projectId)
    {
        $data = $this->reportController->buildReportData($projectId);

        return response()->json(
            $this->ai->explainForecast($this->summarize($data))
        );
    }

    public function senseCheck($projectId)
    {
        $data = $this->reportController->buildReportData($projectId);

        $context = $this->summarize($data);
        $context['risk_signals'] = $this->riskSignals($data);

        return response()->json(
            $this->ai->senseCheck($context)
        );
    }

    /**
     * Historical, non-excluded comparable projects (excluding the project
     * being forecast) with deterministic metrics: name, region, GFA, total
     * rate/m2 (summed across estimating elements), and cost/seat where
     * seating data exists. This is the candidate list the AI selects from —
     * every figure here comes straight from the database.
     */
    private function candidateProjects(int $excludeProjectId): array
    {
        $projects = DB::table('projects')
            ->leftJoin('regions', 'projects.region_id', '=', 'regions.id')
            ->where('projects.project_type', 'historical')
            ->where('projects.exclude_from_estimator', false)
            ->where('projects.id', '!=', $excludeProjectId)
            ->select(
                'projects.id',
                'projects.name',
                'regions.name as region',
                'projects.gross_floor_area',
                'projects.seating_capacity'
            )
            ->get()
            ->keyBy('id');

        $totalRates = DB::table('project_element_costs')
            ->join('projects', 'projects.id', '=', 'project_element_costs.project_id')
            ->where('projects.project_type', 'historical')
            ->where('projects.exclude_from_estimator', false)
            ->where('projects.id', '!=', $excludeProjectId)
            ->groupBy('projects.id')
            ->select('projects.id', DB::raw('SUM(project_element_costs.rate_per_m2) as total_rate_per_m2'))
            ->pluck('total_rate_per_m2', 'id');

        $costPerSeat = DB::table('projects')
            ->join('transactions', 'projects.id', '=', 'transactions.project_id')
            ->where('projects.project_type', 'historical')
            ->where('projects.exclude_from_estimator', false)
            ->whereNotNull('projects.seating_capacity')
            ->where('projects.seating_capacity', '>', 0)
            ->where('projects.id', '!=', $excludeProjectId)
            ->groupBy('projects.id', 'projects.seating_capacity')
            ->select('projects.id', DB::raw('SUM(transactions.amount) / projects.seating_capacity as cost_per_seat'))
            ->pluck('cost_per_seat', 'id');

        return $projects->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'region' => $p->region,
            'gross_floor_area_m2' => $p->gross_floor_area !== null ? round((float) $p->gross_floor_area, 1) : null,
            'seating_capacity' => $p->seating_capacity,
            'total_rate_per_m2' => isset($totalRates[$p->id]) ? round((float) $totalRates[$p->id], 2) : null,
            'cost_per_seat' => isset($costPerSeat[$p->id]) ? round((float) $costPerSeat[$p->id], 2) : null,
        ])->values()->toArray();
    }

    /**
     * Condensed, AI-readable summary of the figures already computed by
     * buildReportData(). Every value here is copied from $data — nothing is
     * recalculated.
     */
    private function summarize(array $data): array
    {
        $summary = [
            'project' => [
                'name' => $data['project']->name,
                'region' => $data['project']->region?->name,
                'gross_floor_area_m2' => (float) ($data['project']->gross_floor_area ?? 0),
                'seating_capacity' => $data['project']->seating_capacity,
            ],
            'area_based' => [
                'low' => round($data['areaLow'], 2),
                'mid' => round($data['areaMid'], 2),
                'high' => round($data['areaHigh'], 2),
                'comparable_projects' => $data['areaBand']['n'],
                'uncertainty_multiplier' => round($data['areaBand']['multiplier'], 2),
                'used_broad_region_spread' => $data['areaUsedBroadSpread'],
            ],
            'seat_based' => null,
            'overall' => [
                'confidence_score_1_to_5' => $data['confidenceScore'],
                'comparable_project_count' => $data['comparableProjectCount'],
                'divergence_pct' => $data['divergencePct'] !== null ? round($data['divergencePct'], 1) : null,
                'divergence_warning' => $data['divergenceWarning'],
                'shrinkage_k' => $data['shrinkageK'],
                'uncertainty_c' => $data['uncertaintyC'],
            ],
            'elements' => [],
        ];

        if ($data['seatMid'] !== null) {
            $summary['seat_based'] = [
                'low' => round($data['seatLow'], 2),
                'mid' => round($data['seatMid'], 2),
                'high' => round($data['seatHigh'], 2),
                'comparable_projects' => $data['seatBand']['n'],
                'uncertainty_multiplier' => round($data['seatBand']['multiplier'], 2),
                'used_broad_region_spread' => $data['seatUsedBroadSpread'],
            ];
        }

        foreach ($data['breakdown'] as $item) {
            $summary['elements'][] = [
                'code' => $item['code'],
                'name' => $item['name'],
                'medium_rate_per_m2' => round($item['blend']['medium'], 2),
                'confidence' => $item['confidence']['label'],
                'comparable_projects' => $item['blend']['n'],
                'was_blended' => $item['blend']['was_blended'],
            ];
        }

        return $summary;
    }

    /**
     * Plain-language risk signals derived from figures already computed by
     * buildReportData(). Used as Sense-Check context — the AI writes
     * advisory text about these, it does not generate them itself.
     */
    private function riskSignals(array $data): array
    {
        $signals = [];

        if ($data['comparableProjectCount'] < 3) {
            $signals[] = "Fewer than 3 comparable projects in this region overall (n={$data['comparableProjectCount']}).";
        }

        if ($data['divergenceWarning']) {
            $pct = round($data['divergencePct'], 1);
            $signals[] = "Area-based and seating-based mid-point forecasts diverge by {$pct}%.";
        }

        if ($data['areaUsedBroadSpread']) {
            $signals[] = 'Area-based Low/High band uses the all-region spread fallback (fewer than 2 local comparable projects).';
        }

        if ($data['seatBand'] !== null && $data['seatUsedBroadSpread']) {
            $signals[] = 'Seat-based Low/High band uses the all-region spread fallback (fewer than 2 local comparable projects).';
        }

        foreach ($data['breakdown'] as $item) {
            if ($item['blend']['was_blended']) {
                $signals[] = "{$item['name']} rate was shrunk toward the all-region mean (local comparable projects n={$item['blend']['n']}).";
            }

            if (in_array($item['confidence']['label'], ['Very Low', 'No data'], true)) {
                $signals[] = "{$item['name']} has {$item['confidence']['label']} confidence (n={$item['confidence']['count']}).";
            }
        }

        return $signals;
    }
}
