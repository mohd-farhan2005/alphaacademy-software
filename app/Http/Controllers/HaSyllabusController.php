<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HaPaper;
use App\Models\HaModule;
use App\Models\HaUnit;
use App\Models\HaUnitCompletion;

class HaSyllabusController extends Controller
{
    public function __construct()
    {
        // Only allow Super Admins or HA users (assuming department name is 'HA')
        // We will just let it pass for now if they are authenticated, but we could restrict it.
    }

    public function index(Request $request)
    {
        $batches = \App\Models\Batch::orderBy('name')->pluck('name')->toArray();
        if (empty($batches)) {
            // Provide some default if completely empty, or just let it be empty
            $batches = ['Batch 1'];
        }
        $batch = $request->input('batch', $batches[0] ?? 'Batch 1');
        
        $papers = HaPaper::with(['modules.units' => function($query) {
            $query->orderBy('id', 'asc');
        }])->get();

        // Get completions for the current batch
        $completions = HaUnitCompletion::where('batch', $batch)->pluck('ha_unit_id')->toArray();

        return view('ha-syllabus.index', compact('papers', 'batch', 'batches', 'completions'));
    }

    public function progress(Request $request)
    {
        $batchesList = \App\Models\Batch::orderBy('name')->pluck('name')->toArray();
        if (empty($batchesList)) {
            $batchesList = ['Batch 1'];
        }
        $batchName = $request->input('batch', $batchesList[0] ?? 'Batch 1');
        
        $batchModel = \App\Models\Batch::where('name', $batchName)->first();
        
        $papers = HaPaper::with(['modules.units' => function($query) {
            $query->orderBy('id', 'asc');
        }])->orderBy('id', 'asc')->get();

        // Get completions for the current batch
        $completions = HaUnitCompletion::where('batch', $batchName)->pluck('ha_unit_id')->toArray();

        return view('ha-syllabus.progress', [
            'papers' => $papers, 
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

    public function storePaper(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        HaPaper::create($validated);

        return redirect()->back()->with('success', 'Paper created successfully.');
    }

    public function storeModule(Request $request)
    {
        $validated = $request->validate([
            'ha_paper_id' => 'required|exists:ha_papers,id',
            'name' => 'required|string|max:255',
        ]);

        HaModule::create($validated);

        return redirect()->back()->with('success', 'Module created successfully.');
    }

    public function storeUnit(Request $request)
    {
        $validated = $request->validate([
            'ha_module_id' => 'required|exists:ha_modules,id',
            'name' => 'required|string|max:255',
        ]);

        HaUnit::create($validated);

        return redirect()->back()->with('success', 'Unit created successfully.');
    }

    public function toggleUnit(Request $request)
    {
        $validated = $request->validate([
            'ha_unit_id' => 'required|exists:ha_units,id',
            'batch' => 'required|string|max:255',
            'completed' => 'required|boolean',
        ]);

        if ($validated['completed']) {
            HaUnitCompletion::firstOrCreate([
                'ha_unit_id' => $validated['ha_unit_id'],
                'batch' => $validated['batch'],
            ]);
        } else {
            HaUnitCompletion::where('ha_unit_id', $validated['ha_unit_id'])
                ->where('batch', $validated['batch'])
                ->delete();
        }

        return response()->json(['success' => true]);
    }

    public function toggleUnitForm(Request $request)
    {
        $validated = $request->validate([
            'ha_unit_id' => 'required|exists:ha_units,id',
            'batch' => 'required|string|max:255',
            'completed' => 'required|boolean',
        ]);

        if ($validated['completed']) {
            HaUnitCompletion::firstOrCreate([
                'ha_unit_id' => $validated['ha_unit_id'],
                'batch' => $validated['batch'],
            ]);
        } else {
            HaUnitCompletion::where('ha_unit_id', $validated['ha_unit_id'])
                ->where('batch', $validated['batch'])
                ->delete();
        }

        return redirect()->route('ha-syllabus.index', ['batch' => $validated['batch']])
            ->with('success', 'Unit status updated successfully.');
    }
}
