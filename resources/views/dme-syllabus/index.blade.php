<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('DME Syllabus Tracker') }}
            </h2>
            
            <form action="{{ route('dme-syllabus.index') }}" method="GET" class="flex items-center gap-2">
                <label for="batch" class="text-sm font-medium text-gray-700">Select Batch:</label>
                <select name="batch" id="batch" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="this.form.submit()">
                    @foreach($batches as $b)
                        <option value="{{ $b }}" {{ $batch == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Add Forms -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Add Batch -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100 hover:shadow-md transition">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Add Batch
                    </h3>
                    <form action="{{ route('dme-syllabus.storeBatch') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. Batch 13" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Syllabus Due Date</label>
                            <input type="date" name="syllabus_due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition font-medium">Add Batch</button>
                    </form>
                </div>

                <!-- Add Sub-Unit -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100 hover:shadow-md transition">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Add Sub-Unit
                    </h3>
                    <form action="{{ route('dme-syllabus.storeSubUnit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Sub-Unit Name" required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition font-medium">Add Sub-Unit</button>
                    </form>
                </div>

                <!-- Add Sub-Module -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100 hover:shadow-md transition">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Add Sub-Module
                    </h3>
                    <form action="{{ route('dme-syllabus.storeSubModule') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <select name="dme_sub_unit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Sub-Unit</option>
                                @foreach($subUnits as $subUnit)
                                    <option value="{{ $subUnit->id }}">{{ $subUnit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Sub-Module Name" required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition font-medium">Add Sub-Module</button>
                    </form>
                </div>
            </div>

            <!-- Syllabus Tracker Status Update -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center justify-between border-b pb-4 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Syllabus Progress for {{ $batch }}</h3>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                    <h4 class="text-md font-medium text-gray-800 mb-3">Update Sub-Module Status</h4>
                    <form action="{{ route('dme-syllabus.toggleSubModuleForm') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                                <select name="batch" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @foreach($batches as $b)
                                        <option value="{{ $b }}" {{ $batch == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sub-Unit</label>
                                <select id="sub-unit-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">Select Sub-Unit...</option>
                                    @foreach($subUnits as $subUnit)
                                        <option value="{{ $subUnit->id }}">{{ $subUnit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sub-Module</label>
                                <select name="dme_sub_module_id" id="sub-module-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required disabled>
                                    <option value="">Select Sub-Module...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="completed" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="1">Completed</option>
                                    <option value="0">Not Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition shadow-sm font-medium">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>



        </div>
    </div>

    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dynamic Dropdowns Logic
            const subUnitsData = @json($subUnits);
            const subUnitSelect = document.getElementById('sub-unit-select');
            const subModuleSelect = document.getElementById('sub-module-select');

            if(subUnitSelect) {
                subUnitSelect.addEventListener('change', function() {
                    const subUnitId = this.value;
                    subModuleSelect.innerHTML = '<option value="">Select Sub-Module...</option>';
                    subModuleSelect.disabled = true;

                    if (subUnitId) {
                        const subUnit = subUnitsData.find(su => su.id == subUnitId);
                        if (subUnit && subUnit.sub_modules && subUnit.sub_modules.length > 0) {
                            subUnit.sub_modules.forEach(mod => {
                                const option = document.createElement('option');
                                option.value = mod.id;
                                option.textContent = mod.name;
                                subModuleSelect.appendChild(option);
                            });
                            subModuleSelect.disabled = false;
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
