<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $data = [];

        if ($user->role === 'admin') {
            $data = [
                'pending_transactions' => Transaction::count(),
                'last_import' => Transaction::latest('created_at')->first()?->created_at,
                'total_transaction_count' => Transaction::count(),
                'total_amount' => Transaction::sum('amount'),
                'active_projects' => Project::count(),
            ];
        } elseif ($user->role === 'cost_manager') {
            $data = [
                'recent_estimates' => 0,
                'has_estimator' => true,
            ];
        } elseif ($user->role === 'reviewer') {
            $data = [
                'active_projects' => Project::count(),
                'has_analytics' => true,
            ];
        }

        return view('dashboard', $data);
    }
}
