<?php

namespace App\Console\Commands;

use App\Models\EstimatingElement;
use App\Models\Project;
use App\Models\ProjectElementCost;
use App\Models\Region;
use App\Models\RegionalRate;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateRegionalRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate project element costs and regional parametric rates from transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting rate calculation...');
        $this->info('');

        // Step 1: Calculate project element costs
        $this->calculateProjectElementCosts();

        // Step 2: Calculate regional rates
        $this->calculateRegionalRates();

        $this->info('');
        $this->info('Rate calculation complete!');
    }

    /**
     * Calculate total costs per project per estimating element
     */
    private function calculateProjectElementCosts(): void
    {
        $this->info('Step 1: Calculating project element costs from transactions...');

        $projects = Project::whereNotNull('gross_floor_area')->get();
        $elements = EstimatingElement::all();

        $totalCalculated = 0;

        foreach ($projects as $project) {
            foreach ($elements as $element) {
                // Sum all transactions for this project + element combination
                $totalCost = Transaction::whereHas('pdCode', function ($query) use ($element) {
                    $query->where('estimating_element_id', $element->id);
                })
                    ->where('project_id', $project->id)
                    ->sum('amount');

                if ($totalCost > 0) {
                    $ratePerM2 = $totalCost / $project->gross_floor_area;

                    ProjectElementCost::updateOrCreate(
                        [
                            'project_id' => $project->id,
                            'estimating_element_id' => $element->id,
                        ],
                        [
                            'total_cost' => $totalCost,
                            'gross_floor_area' => $project->gross_floor_area,
                            'rate_per_m2' => $ratePerM2,
                            'calculated_at' => now(),
                        ]
                    );

                    $totalCalculated++;
                }
            }
        }

        $this->info("  ✓ Calculated costs for $totalCalculated project-element combinations");
    }

    /**
     * Calculate regional rates (Low/Medium/High/High+) from project element costs
     */
    private function calculateRegionalRates(): void
    {
        $this->info('Step 2: Calculating regional rates...');

        $regions = Region::all();
        $elements = EstimatingElement::all();

        $totalCalculated = 0;

        foreach ($regions as $region) {
            foreach ($elements as $element) {
                // Get all rates for this region + element
                $rates = ProjectElementCost::whereHas('project', function ($query) use ($region) {
                    $query->where('region_id', $region->id)
                        ->where('exclude_from_estimator', false);
                })
                    ->where('estimating_element_id', $element->id)
                    ->whereNotNull('rate_per_m2')
                    ->pluck('rate_per_m2')
                    ->toArray();

                if (count($rates) > 0) {
                    sort($rates);

                    $lowRate = min($rates);
                    $mediumRate = array_sum($rates) / count($rates);
                    $highRate = max($rates);
                    $highPlusRate = $highRate * 1.15; // High + 15%

                    // Get min/max project numbers for audit trail
                    $projectNumbers = ProjectElementCost::whereHas('project', function ($query) use ($region) {
                        $query->where('region_id', $region->id)
                            ->where('exclude_from_estimator', false);
                    })
                        ->where('estimating_element_id', $element->id)
                        ->whereNotNull('rate_per_m2')
                        ->with('project')
                        ->get()
                        ->pluck('project.project_nr')
                        ->toArray();

                    RegionalRate::updateOrCreate(
                        [
                            'region_id' => $region->id,
                            'estimating_element_id' => $element->id,
                        ],
                        [
                            'low_rate' => round($lowRate, 2),
                            'medium_rate' => round($mediumRate, 2),
                            'high_rate' => round($highRate, 2),
                            'high_plus_rate' => round($highPlusRate, 2),
                            'project_count' => count($rates),
                            'min_project_nr' => min($projectNumbers) ?? null,
                            'max_project_nr' => max($projectNumbers) ?? null,
                            'calculated_at' => now(),
                        ]
                    );

                    $totalCalculated++;
                }
            }
        }

        $this->info("  ✓ Calculated rates for $totalCalculated region-element combinations");
    }
}
