<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DmeSubUnit;
use App\Models\DmeSubModule;
use App\Models\DmeSubModuleCompletion;

class DmeSyllabusController extends Controller
{
    public function __construct()
    {
        // Add auth/role logic here if needed
    }

    public function index(Request $request)
    {
        $batches = \App\Models\Batch::orderBy('name')->pluck('name')->toArray();
        if (empty($batches)) {
            $batches = ['Batch 1'];
        }
        $batch = $request->input('batch', $batches[0] ?? 'Batch 1');
        
        $subUnits = DmeSubUnit::with(['subModules' => function($query) {
            $query->orderBy('id', 'asc');
        }])->get();

        // Get completions for the current batch
        $completions = DmeSubModuleCompletion::where('batch', $batch)->pluck('dme_sub_module_id')->toArray();

        return view('dme-syllabus.index', compact('subUnits', 'batch', 'batches', 'completions'));
    }

    public function progress(Request $request)
    {
        $batchesList = \App\Models\Batch::orderBy('name')->pluck('name')->toArray();
        if (empty($batchesList)) {
            $batchesList = ['Batch 1'];
        }
        $batchName = $request->input('batch', $batchesList[0] ?? 'Batch 1');
        
        $batchModel = \App\Models\Batch::where('name', $batchName)->first();
        
        $subUnits = DmeSubUnit::with(['subModules' => function($query) {
            $query->orderBy('id', 'asc');
        }])->orderBy('id', 'asc')->get();

        // Get completions for the current batch
        $completions = DmeSubModuleCompletion::where('batch', $batchName)->pluck('dme_sub_module_id')->toArray();

        return view('dme-syllabus.progress', [
            'subUnits' => $subUnits, 
            'batch' => $batchName, 
            'batches' => $batchesList, 
            'completions' => $completions,
            'batchModel' => $batchModel
        ]);
    }

    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:batches,name',
            'syllabus_due_date' => 'required|date',
        ]);

        \App\Models\Batch::create($validated);

        return redirect()->back()->with('success', 'Batch created successfully.');
    }

    public function storeSubUnit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DmeSubUnit::create($validated);

        return redirect()->back()->with('success', 'Sub-Unit created successfully.');
    }

    public function updateSubUnit(Request $request, $id)
    {
        $subUnit = DmeSubUnit::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $subUnit->update($validated);

        return redirect()->back()->with('success', 'Sub-Unit updated successfully.');
    }

    public function destroySubUnit($id)
    {
        $subUnit = DmeSubUnit::findOrFail($id);
        $subUnit->delete();

        return redirect()->back()->with('success', 'Sub-Unit deleted successfully.');
    }

    public function storeSubModule(Request $request)
    {
        $validated = $request->validate([
            'dme_sub_unit_id' => 'required|exists:dme_sub_units,id',
            'name' => 'required|string|max:255',
        ]);

        DmeSubModule::create($validated);

        return redirect()->back()->with('success', 'Sub-Module created successfully.');
    }

    public function updateSubModule(Request $request, $id)
    {
        $subModule = DmeSubModule::findOrFail($id);
        $validated = $request->validate([
            'dme_sub_unit_id' => 'required|exists:dme_sub_units,id',
            'name' => 'required|string|max:255',
        ]);

        $subModule->update($validated);

        return redirect()->back()->with('success', 'Sub-Module updated successfully.');
    }

    public function destroySubModule($id)
    {
        $subModule = DmeSubModule::findOrFail($id);
        $subModule->delete();

        return redirect()->back()->with('success', 'Sub-Module deleted successfully.');
    }

    public function toggleSubModule(Request $request)
    {
        $validated = $request->validate([
            'dme_sub_module_id' => 'required|exists:dme_sub_modules,id',
            'batch' => 'required|string|max:255',
            'completed' => 'required|boolean',
        ]);

        if ($validated['completed']) {
            DmeSubModuleCompletion::firstOrCreate([
                'dme_sub_module_id' => $validated['dme_sub_module_id'],
                'batch' => $validated['batch'],
            ]);
        } else {
            DmeSubModuleCompletion::where('dme_sub_module_id', $validated['dme_sub_module_id'])
                ->where('batch', $validated['batch'])
                ->delete();
        }

        return response()->json(['success' => true]);
    }

    public function toggleSubModuleForm(Request $request)
    {
        $validated = $request->validate([
            'dme_sub_module_id' => 'required|exists:dme_sub_modules,id',
            'batch' => 'required|string|max:255',
            'completed' => 'required|boolean',
        ]);

        if ($validated['completed']) {
            DmeSubModuleCompletion::firstOrCreate([
                'dme_sub_module_id' => $validated['dme_sub_module_id'],
                'batch' => $validated['batch'],
            ]);
        } else {
            DmeSubModuleCompletion::where('dme_sub_module_id', $validated['dme_sub_module_id'])
                ->where('batch', $validated['batch'])
                ->delete();
        }

        return redirect()->route('dme-syllabus.index', ['batch' => $validated['batch']])
            ->with('success', 'Sub-Module status updated successfully.');
    }
}
