<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('DME Syllabus Progress List') }}
            </h2>
            
            <form action="{{ route('dme-syllabus.progress') }}" method="GET" class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
                <label for="batch" class="text-sm font-semibold text-gray-600 tracking-wide uppercase">Select Batch:</label>
                <div class="relative">
                    <select name="batch" id="batch" class="appearance-none pl-4 pr-10 py-2 w-48 bg-gray-50 border-gray-200 text-gray-800 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-medium shadow-inner transition-colors cursor-pointer hover:bg-gray-100" onchange="this.form.submit()">
                        @foreach($batches as $b)
                            <option value="{{ $b }}" {{ $batch == $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl border border-gray-100">
                <!-- Premium Header Section -->
                <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800 px-8 py-10 md:px-12 md:py-14 text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10">
                        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                                    <path d="M0 40L40 0H20L0 20M40 40V20L20 40" stroke="currentColor" stroke-width="2" fill="none"/>
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#grid-pattern)"/>
                        </svg>
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between">
                        <div>
                            <h3 class="text-4xl font-extrabold tracking-tight mb-2">DME Syllabus Progress</h3>
                            <div class="flex items-center flex-wrap gap-3">
                                <p class="text-indigo-100 text-xl font-light">Tracking completion for <span class="font-bold text-white bg-white/20 px-3 py-1 rounded-lg ml-2 shadow-sm">{{ $batch }}</span></p>
                                @if(isset($batchModel) && $batchModel->syllabus_due_date)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500/20 text-red-100 border border-red-500/30">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Due: {{ \Carbon\Carbon::parse($batchModel->syllabus_due_date)->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-8 md:p-12">
                    @if($subUnits->isEmpty())
                        <div class="text-center py-16">
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gray-100 mb-6 shadow-inner">
                                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">No Syllabus Data Available</h3>
                            <p class="text-gray-500 max-w-md mx-auto">It looks like there are no sub-units assigned to this department yet. Please add them from the Tracker page.</p>
                        </div>
                    @else
                        <div class="space-y-12">
                            @foreach($subUnits as $subUnit)
                                @php
                                    $unitTotalSubModules = $subUnit->subModules->count();
                                    $unitCompletedSubModules = 0;
                                    foreach($subUnit->subModules as $subModule) {
                                        if(in_array($subModule->id, $completions)) {
                                            $unitCompletedSubModules++;
                                        }
                                    }
                                    $isUnitComplete = $unitTotalSubModules > 0 && $unitTotalSubModules === $unitCompletedSubModules;
                                    $unitProgressPercent = $unitTotalSubModules > 0 ? round(($unitCompletedSubModules / $unitTotalSubModules) * 100) : 0;
                                @endphp

                                <div class="bg-white rounded-2xl border {{ $isUnitComplete ? 'border-green-200 shadow-green-100/50' : 'border-gray-100 shadow-indigo-100/30' }} shadow-xl overflow-hidden transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl">
                                    
                                    <!-- Sub-Unit Header -->
                                    <div class="px-8 py-6 {{ $isUnitComplete ? 'bg-gradient-to-r from-green-50 to-emerald-50' : 'bg-gray-50' }} border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div>
                                            <h4 class="text-2xl font-black text-gray-800 flex items-center gap-4 tracking-tight">
                                                {{ $subUnit->name }}
                                                @if($isUnitComplete)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500 text-white shadow-sm uppercase tracking-wider animate-pulse">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                                        Fully Completed
                                                    </span>
                                                @endif
                                            </h4>
                                        </div>
                                        <div class="text-left md:text-right flex items-center md:block gap-4">
                                            <span class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Progress</span>
                                            <div class="text-3xl font-black {{ $isUnitComplete ? 'text-green-600' : 'text-indigo-600' }}">
                                                {{ $unitCompletedSubModules }} <span class="text-lg text-gray-400 font-medium">/ {{ $unitTotalSubModules }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Overall Sub-Unit Progress Bar -->
                                    <div class="w-full bg-gray-200 h-2">
                                        <div class="{{ $isUnitComplete ? 'bg-green-500' : 'bg-indigo-500' }} h-2 transition-all duration-1000 ease-out relative" style="width: {{ $unitProgressPercent }}%">
                                            @if($unitProgressPercent > 0 && !$isUnitComplete)
                                                <div class="absolute right-0 top-0 bottom-0 w-4 bg-white/30 animate-pulse"></div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Sub-Modules List -->
                                    <div class="p-8">
                                        <div class="flex flex-wrap gap-4">
                                            @foreach($subUnit->subModules as $subModule)
                                                @php $isSubModuleComplete = in_array($subModule->id, $completions); @endphp
                                                <div class="inline-flex items-center px-5 py-3 rounded-xl text-sm font-semibold border shadow-sm transition-all duration-200 {{ $isSubModuleComplete ? 'bg-green-50 border-green-200 text-green-700 hover:bg-green-100' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300' }}">
                                                    @if($isSubModuleComplete)
                                                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-200 text-green-700 mr-3">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                                        </span>
                                                    @else
                                                        <span class="flex items-center justify-center w-6 h-6 rounded-full border-2 border-gray-300 mr-3"></span>
                                                    @endif
                                                    <span class="{{ $isSubModuleComplete ? 'opacity-90' : '' }} text-base">{{ $subModule->name }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
