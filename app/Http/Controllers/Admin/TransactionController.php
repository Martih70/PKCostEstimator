<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\PdCode;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('pdCode', 'project');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%$search%")
                ->orWhereHas('pdCode', fn($q) => $q->where('code', 'like', "%$search%"));
        }

        $transactions = $query->orderByDesc('date')->paginate(50);

        return view('admin.transactions.index', compact('transactions'));
    }

    public function create()
    {
        $pdCodes = PdCode::orderBy('code')->get(['id', 'code', 'description']);
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.transactions.create', compact('pdCodes', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date_format:Y-m-d',
            'pd_code_id' => 'required|exists:pd_codes,id',
            'project_id' => 'required|exists:projects,id',
            'item_description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        // Add required raw fields
        $validated['raw_pd_code'] = PdCode::find($validated['pd_code_id'])->code;
        $validated['raw_project_nr'] = Project::find($validated['project_id'])->name;
        $validated['import_batch'] = 'manual_entry';

        Transaction::create($validated);

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction created.');
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);
        $pdCodes = PdCode::orderBy('code')->get(['id', 'code', 'description']);
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('admin.transactions.edit', compact('transaction', 'pdCodes', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'transaction_date' => 'required|date_format:Y-m-d',
            'pd_code_id' => 'required|exists:pd_codes,id',
            'project_id' => 'required|exists:projects,id',
            'item_description' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $transaction->update($validated);

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction updated.');
    }

    public function destroy($id)
    {
        Transaction::findOrFail($id)->delete();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction deleted.');
    }

    public function importForm()
    {
        return view('admin.transactions.import');
    }

    public function processImport(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        $file = $request->file('file')->getPathname();
        Artisan::call('import:transactions', ['file' => $file]);

        return redirect()->route('admin.transactions.index')->with('success', 'CSV imported successfully.');
    }
}
