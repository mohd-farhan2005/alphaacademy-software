<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('HA Syllabus Tracker') }}
            </h2>
            
            <form action="{{ route('ha-syllabus.index') }}" method="GET" class="flex items-center gap-2">
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Add Batch -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Batch</h3>
                    <form action="{{ route('ha-syllabus.storeBatch') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. Batch 13" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Syllabus Due Date</label>
                            <input type="date" name="syllabus_due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Add Batch</button>
                    </form>
                </div>

                <!-- Add Paper -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Paper / Subject</h3>
                    <form action="{{ route('ha-syllabus.storePaper') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Paper Name" required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Add Paper</button>
                    </form>
                </div>

                <!-- Add Module -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Module</h3>
                    <form action="{{ route('ha-syllabus.storeModule') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <select name="ha_paper_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Paper</option>
                                @foreach($papers as $paper)
                                    <option value="{{ $paper->id }}">{{ $paper->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Module Name" required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Add Module</button>
                    </form>
                </div>

                <!-- Add Unit -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Unit</h3>
                    <form action="{{ route('ha-syllabus.storeUnit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <select name="ha_module_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Select Module</option>
                                @foreach($papers as $paper)
                                    <optgroup label="{{ $paper->name }}">
                                        @foreach($paper->modules as $module)
                                            <option value="{{ $module->id }}">{{ $module->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Unit Name" required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Add Unit</button>
                    </form>
                </div>
            </div>

            <!-- Syllabus Tracker -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between border-b pb-4 mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Syllabus Progress for {{ $batch }}</h3>
                </div>

                <!-- Update Unit Status Form -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                    <h4 class="text-md font-medium text-gray-800 mb-3">Update Unit Status</h4>
                    <form action="{{ route('ha-syllabus.toggleUnitForm') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Batch</label>
                                <select name="batch" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @foreach($batches as $b)
                                        <option value="{{ $b }}" {{ $batch == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Paper</label>
                                <select id="paper-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">Select Paper...</option>
                                    @foreach($papers as $paper)
                                        <option value="{{ $paper->id }}">{{ $paper->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                                <select id="module-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required disabled>
                                    <option value="">Select Module...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                                <select name="ha_unit_id" id="unit-select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required disabled>
                                    <option value="">Select Unit...</option>
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
            const papersData = @json($papers);
            const paperSelect = document.getElementById('paper-select');
            const moduleSelect = document.getElementById('module-select');
            const unitSelect = document.getElementById('unit-select');

            if(paperSelect) {
                paperSelect.addEventListener('change', function() {
                    const paperId = this.value;
                    moduleSelect.innerHTML = '<option value="">Select Module...</option>';
                    unitSelect.innerHTML = '<option value="">Select Unit...</option>';
                    moduleSelect.disabled = true;
                    unitSelect.disabled = true;

                    if (paperId) {
                        const paper = papersData.find(p => p.id == paperId);
                        if (paper && paper.modules.length > 0) {
                            paper.modules.forEach(mod => {
                                const option = document.createElement('option');
                                option.value = mod.id;
                                option.textContent = mod.name;
                                moduleSelect.appendChild(option);
                            });
                            moduleSelect.disabled = false;
                        }
                    }
                });
            }

            if(moduleSelect) {
                moduleSelect.addEventListener('change', function() {
                    const moduleId = this.value;
                    const paperId = paperSelect.value;
                    unitSelect.innerHTML = '<option value="">Select Unit...</option>';
                    unitSelect.disabled = true;

                    if (moduleId && paperId) {
                        const paper = papersData.find(p => p.id == paperId);
                        const module = paper.modules.find(m => m.id == moduleId);
                        if (module && module.units.length > 0) {
                            module.units.forEach(unit => {
                                const option = document.createElement('option');
                                option.value = unit.id;
                                option.textContent = unit.name;
                                unitSelect.appendChild(option);
                            });
                            unitSelect.disabled = false;
                        }
                    }
                });
            }

        });
    </script>
</x-app-layout>
