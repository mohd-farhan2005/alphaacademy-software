<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::latest();
        
        if ($request->filled('batch')) {
            $query->where('batch', $request->batch);
        }
        
        $invoices = $query->get();
        $batches = Invoice::select('batch')->whereNotNull('batch')->distinct()->pluck('batch');
        
        return view('invoices.index', compact('invoices', 'batches'));
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'batch' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'service' => 'required|in:Fees,Software',
            'payment_type' => 'required|in:Cash,Upi',
        ]);

        $invoice = Invoice::create($validated);

        if ($request->has('save_and_print')) {
            return redirect()->route('invoices.show', $invoice->id);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'batch' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'service' => 'required|in:Fees,Software',
            'payment_type' => 'required|in:Cash,Upi',
        ]);

        $invoice->update($validated);

        if ($request->has('save_and_print')) {
            return redirect()->route('invoices.show', $invoice->id);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
