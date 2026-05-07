<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="student_name" class="block text-sm font-medium text-gray-700">Student Name</label>
                            <input type="text" name="student_name" id="student_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required value="{{ old('student_name', $invoice->student_name) }}">
                            @error('student_name')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="batch" class="block text-sm font-medium text-gray-700">Batch</label>
                            <select name="batch" id="batch" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select Batch (Optional)</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="Batch {{ $i }}" {{ old('batch', $invoice->batch) == "Batch $i" ? 'selected' : '' }}>Batch {{ $i }}</option>
                                @endfor
                            </select>
                            @error('batch')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                            <input type="number" name="price" id="price" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required value="{{ old('price', $invoice->price) }}">
                            @error('price')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
                            <select name="service" id="service" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Service</option>
                                <option value="Fees" {{ old('service', $invoice->service) == 'Fees' ? 'selected' : '' }}>Fees</option>
                                <option value="Software" {{ old('service', $invoice->service) == 'Software' ? 'selected' : '' }}>Software</option>
                            </select>
                            @error('service')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                            <select name="payment_type" id="payment_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Payment Type</option>
                                <option value="Cash" {{ old('payment_type', $invoice->payment_type) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Upi" {{ old('payment_type', $invoice->payment_type) == 'Upi' ? 'selected' : '' }}>Upi</option>
                            </select>
                            @error('payment_type')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4 gap-4">
                            <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">Cancel</a>
                            <button type="submit" name="save" value="1" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Update</button>
                            <button type="submit" name="save_and_print" value="1" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Update & Print</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
