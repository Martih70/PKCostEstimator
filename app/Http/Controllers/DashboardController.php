<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Project;
use App\Models\ProjectElementCost;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $data = [];

        if ($user->role === 'admin') {
            // Calculate total estimated cost for all forecast projects
            $totalEstimatedCost = ProjectElementCost::whereHas('project', function ($query) {
                $query->where('project_type', 'forecast');
            })->sum('total_cost');

            // Get all forecast projects with their region
            $forecastProjects = Project::where('project_type', 'forecast')
                ->with('region')
                ->orderBy('name')
                ->get();

            $data = [
                'pending_transactions' => Transaction::count(),
                'last_import' => Transaction::latest('created_at')->first()?->created_at,
                'total_transaction_count' => Transaction::count(),
                'total_amount' => $totalEstimatedCost,
                'active_projects' => Project::where('project_type', 'forecast')->count(),
                'forecastProjects' => $forecastProjects,
            ];
        } elseif ($user->role === 'cost_manager') {
            $data = [
                'recent_estimates' => 0,
                'has_estimator' => true,
            ];
        } elseif ($user->role === 'reviewer') {
            $data = [
                'active_projects' => Project::where('project_type', 'forecast')->count(),
                'has_analytics' => true,
            ];
        }

        return view('dashboard', $data);
    }
}
