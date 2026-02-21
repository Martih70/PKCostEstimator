<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\EstimatingElement;
use App\Models\RegionalRate;
use App\Models\ExchangeRate;
use App\Models\Transaction;

class ProjectController extends Controller
{
    public function index()
    {
        $allProjects = Project::with('region')->orderBy('region_id')->orderBy('project_number')->get();

        return view('projects.index', [
            'allProjects' => $allProjects,
        ]);
    }

    public function summary(Project $project)
    {
        $project->load('region');
        $elements = EstimatingElement::orderBy('sort_order')->get(['id', 'code', 'name']);

        // Get project element costs keyed by estimating_element_id
        $elementCosts = $project->projectElementCosts()
            ->pluck('rate_per_m2', 'estimating_element_id');

        // Get regional rates for this project's region
        $regionalRates = RegionalRate::where('region_id', $project->region_id)
            ->get()
            ->mapWithKeys(function ($rate) {
                return [$rate->estimating_element_id => [
                    'low' => (float) $rate->low_rate,
                    'medium' => (float) $rate->medium_rate,
                    'high' => (float) $rate->high_rate,
                    'high_plus' => (float) $rate->high_plus_rate,
                ]];
            });

        $exchangeRates = ExchangeRate::orderByDesc('effective_date')
            ->get()->unique('currency_code')->keyBy('currency_code');

        $grandTotal = $elementCosts->sum();
        $allProjects = Project::with('region')->orderBy('region_id')->orderBy('project_number')->get();

        return view('projects.summary', [
            'project' => $project,
            'elements' => $elements,
            'elementCosts' => $elementCosts,
            'regionalRates' => $regionalRates,
            'exchangeRates' => $exchangeRates,
            'grandTotal' => $grandTotal,
            'allProjects' => $allProjects,
        ]);
    }

    public function transactions(Project $project)
    {
        $project->load('region');
        $elements = EstimatingElement::orderBy('sort_order')->get(['id', 'code', 'name']);

        // Get all transactions for this project, ordered by PD code MH sort order
        $transactions = Transaction::where('project_id', $project->id)
            ->with(['pdCode.estimatingElement'])
            ->join('pd_codes', 'transactions.pd_code_id', '=', 'pd_codes.id')
            ->orderBy('pd_codes.mh_sort_order')
            ->orderBy('transactions.transaction_date')
            ->select('transactions.*')
            ->get();

        // Filter out transactions without an estimating element (overhead codes)
        $transactions = $transactions->filter(fn($t) => $t->pdCode?->estimatingElement !== null);

        // Group transactions by estimating element
        $grouped = $transactions->groupBy('pdCode.estimatingElement.id');

        // Calculate subtotals per element
        $subtotals = [];
        foreach ($elements as $element) {
            $subtotals[$element->id] = $grouped->get($element->id, collect())->sum('amount');
        }

        $grandTotal = array_sum($subtotals);

        // Elements with data in this project
        $activeElementIds = $grouped->keys()->toArray();

        // Filter element if query param provided
        $filterElement = request('element');
        if ($filterElement) {
            $grouped = collect([
                $filterElement => $grouped->get($filterElement, collect()),
            ])->filter(fn($items) => $items->isNotEmpty());
        }

        $allProjects = Project::with('region')->orderBy('region_id')->orderBy('project_number')->get();

        return view('projects.transactions', [
            'project' => $project,
            'elements' => $elements,
            'grouped' => $grouped,
            'subtotals' => $subtotals,
            'grandTotal' => $grandTotal,
            'activeElementIds' => $activeElementIds,
            'filterElement' => $filterElement,
            'allProjects' => $allProjects,
        ]);
    }
}
