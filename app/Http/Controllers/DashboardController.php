<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Project;
use App\Models\ProjectElementCost;
use App\Models\RegionalRate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $data = [];

        if ($user->role === 'admin') {
            $forecastProjects = Project::where('project_type', 'forecast')
                ->with('region')
                ->orderBy('name')
                ->get();

            $spendByRegion = DB::table('transactions')
                ->join('projects', 'transactions.project_id', '=', 'projects.id')
                ->join('regions', 'projects.region_id', '=', 'regions.id')
                ->groupBy('regions.id', 'regions.name')
                ->selectRaw('regions.name as region_name, SUM(transactions.amount) as total')
                ->orderByDesc('total')
                ->get();

            $data = [
                'total_transaction_count'  => Transaction::count(),
                'historical_spend'         => Transaction::sum('amount'),
                'historical_projects'      => Project::where('project_type', 'historical')->count(),
                'forecast_projects'        => Project::where('project_type', 'forecast')->count(),
                'regions_covered'          => RegionalRate::distinct('region_id')->count('region_id'),
                'rate_data_points'         => RegionalRate::count(),
                'user_count'               => User::count(),
                'last_import'              => Transaction::latest('created_at')->first()?->created_at,
                'forecastProjects'         => $forecastProjects,
                'spendByRegion'            => $spendByRegion,
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
