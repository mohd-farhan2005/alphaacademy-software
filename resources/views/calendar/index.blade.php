<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Calendar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @php
                        $startOfMonth = $currentDate->copy()->startOfMonth();
                        $endOfMonth = $currentDate->copy()->endOfMonth();
                        $startOfWeek = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                        $endOfWeek = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                        $days = [];
                        $current = $startOfWeek->copy();
                        while ($current <= $endOfWeek) {
                            $days[] = $current->copy();
                            $current->addDay();
                        }
                        
                        $canEdit = in_array(Auth::user()->role, [
                            \App\Enums\RoleType::SUPER_ADMIN, 
                            \App\Enums\RoleType::DME_HEAD, 
                            \App\Enums\RoleType::HA_HEAD
                        ]);
                    @endphp

                    <!-- Calendar Header Controls -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('calendar.index', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}" class="text-gray-500 hover:text-gray-900 transition-colors shrink-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <h3 class="text-2xl font-bold text-gray-800 text-center w-48 uppercase tracking-wider">
                                {{ $currentDate->format('F') }} <span class="text-gray-400 text-xl font-medium">{{ $currentDate->year }}</span>
                            </h3>
                            <a href="{{ route('calendar.index', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}" class="text-gray-500 hover:text-gray-900 transition-colors shrink-0">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            @if($canEdit)
                                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-event-modal')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Add Event') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50/50">
                        <div class="grid grid-cols-7 border-b border-gray-200 bg-[#eef2f6]">
                            @foreach(['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'] as $dayName)
                                <div class="py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-200 last:border-r-0">
                                    {{ $dayName }}
                                </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-7 auto-rows-[minmax(120px,_1fr)]">
                            @foreach($days as $day)
                                @php
                                    $isCurrentMonth = $day->month === $currentDate->month;
                                    $dateString = $day->format('Y-m-d');
                                    $dayEvents = isset($groupedEvents[$dateString]) ? $groupedEvents[$dateString] : collect();
                                @endphp

                                <div class="bg-white border-b border-r border-gray-200 p-2 hover:bg-gray-50 transition-colors {{ !$isCurrentMonth ? 'text-gray-400 bg-gray-50/50' : 'text-gray-700' }} relative group min-h-[120px]">
                                    <div class="flex justify-end mb-1">
                                        <span class="text-sm {{ $day->isToday() ? 'bg-indigo-600 text-white rounded-full w-7 h-7 flex items-center justify-center font-bold' : '' }}">
                                            {{ $day->day }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex flex-col space-y-1">
                                        @foreach($dayEvents as $event)
                                            <div class="relative rounded px-2 py-1 text-xs truncate
                                                {{ $event->type === 'program' ? 'bg-green-100 text-green-800 border border-green-200' : '' }}
                                                {{ $event->type === 'holiday' ? 'bg-red-100 text-red-800 border border-red-200' : '' }}
                                            " title="{{ $event->title }} - {{ $event->description }}">
                                                
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium truncate block mr-1">{{ $event->title }}</span>
                                                    @if($canEdit)
                                                        <form method="POST" action="{{ route('calendar.destroy', $event) }}" class="inline z-10" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-gray-400 hover:text-gray-800 hover:bg-white/50 rounded-full p-0.5" title="Delete Event">
                                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @if(isset($canEdit) && $canEdit)
    <x-modal name="add-event-modal" focusable>
        <form method="post" action="{{ route('calendar.store') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Add New Calendar Event') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="title" value="{{ __('Title') }}" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div class="mt-6">
                <x-input-label for="type" value="{{ __('Type') }}" />
                <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="program">Program</option>
                    <option value="holiday">Holiday</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('type')" />
            </div>

            <div class="mt-6">
                <x-input-label for="date" value="{{ __('Date') }}" />
                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required />
                <x-input-error class="mt-2" :messages="$errors->get('date')" />
            </div>
            
            <div class="mt-6">
                <x-input-label for="description" value="{{ __('Description (Optional)') }}" />
                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Save Event') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
    @endif
</x-app-layout>
