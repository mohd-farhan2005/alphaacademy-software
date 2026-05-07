<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoices') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4 items-center">
                        <h3 class="text-lg font-bold">All Invoices</h3>
                        <div class="flex gap-4">
                            <form action="{{ route('invoices.index') }}" method="GET" class="flex gap-2">
                                <select name="batch" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">All Batches</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="Batch {{ $i }}" {{ request('batch') == "Batch $i" ? 'selected' : '' }}>Batch {{ $i }}</option>
                                    @endfor
                                </select>
                            </form>
                            <a href="{{ route('invoices.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Add Invoice</a>
                        </div>
                    </div>
                    
                    @if(session('success'))
                        <div class="mb-4 text-green-700 bg-green-100 border border-green-200 p-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="p-3 text-sm font-semibold text-gray-700">ID</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Student Name</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Service</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Batch</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Price</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Payment Type</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 text-gray-600 font-medium">{{ $invoice->invoice_number }}</td>
                                    <td class="p-3 font-medium text-gray-900">{{ $invoice->student_name }}</td>
                                    <td class="p-3 text-gray-600">{{ $invoice->service }}</td>
                                    <td class="p-3 text-gray-600">
                                        @if($invoice->batch)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $invoice->batch }}</span>
                                        @else
                                            <span class="text-gray-400 text-sm">N/A</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-gray-600">₹{{ number_format($invoice->price, 2) }}</td>
                                    <td class="p-3 text-gray-600">{{ $invoice->payment_type }}</td>
                                    <td class="p-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-green-600 hover:text-green-900" title="Print">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                            <a href="{{ route('invoices.edit', $invoice) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-gray-500">No invoices found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
