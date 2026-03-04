<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Performance Report: ') . $employee->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filter Navigation -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex justify-center sm:justify-end gap-2">
                <a href="{{ route('employees.show', ['employee' => $employee, 'period' => 'this_month']) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $period === 'this_month' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    This Month
                </a>
                <a href="{{ route('employees.show', ['employee' => $employee, 'period' => 'last_month']) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $period === 'last_month' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Last Month
                </a>
                <a href="{{ route('employees.show', ['employee' => $employee, 'period' => 'all_time']) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $period === 'all_time' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Time
                </a>
            </div>
            
            <!-- Employee Details -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-wrap gap-6 justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500">Employee Name</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email Address</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Department</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $employee->department ? $employee->department->name : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Role</p>
                    <p class="inline-flex items-center px-2 py-1 mt-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        {{ str_replace('_', ' ', strtoupper($employee->role->value ?? '')) }}
                    </p>
                </div>
            </div>

            <!-- Stats & Graph -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Stat Cards -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-400">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Tasks</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $taskCounts['pending'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                         <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">In Progress Tasks</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $taskCounts['in_progress'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                         <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed Tasks</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $taskCounts['completed'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart -->
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col items-center justify-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 self-start">Task Distribution ({{ ucwords(str_replace('_', ' ', $period)) }})</h3>
                    <div class="w-full max-w-sm">
                        @if($taskCounts['pending'] == 0 && $taskCounts['in_progress'] == 0 && $taskCounts['completed'] == 0)
                            <div class="text-center text-gray-500 py-10">No tasks assigned yet.</div>
                        @else
                            <canvas id="performanceChart"></canvas>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Tasks Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 mt-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Assigned Tasks</h3>
                        <a href="{{ route('tasks.create') }}?assigned_to={{ $employee->id }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md transition text-sm font-medium whitespace-nowrap">
                            Add New Task
                        </a>
                    </div>
                    @if($recentTasks->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="p-3 text-sm font-semibold text-gray-700">Title</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Status</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Due Date</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTasks as $task)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-medium text-gray-900 text-sm">{{ $task->title }}</td>
                                    <td class="p-3 text-sm">
                                        @if($task->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">In Progress</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-gray-600 text-sm">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : '-' }}</td>
                                    <td class="p-3 text-sm">
                                        <a href="{{ route('tasks.index') }}?search={{ urlencode($task->title) }}" class="text-indigo-600 hover:text-indigo-900">View in Tasks</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-6 text-gray-500">
                        <p>No recent tasks found.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($taskCounts['pending'] > 0 || $taskCounts['in_progress'] > 0 || $taskCounts['completed'] > 0)
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'In Progress', 'Completed'],
                    datasets: [{
                        data: [
                            {{ $taskCounts['pending'] }}, 
                            {{ $taskCounts['in_progress'] }}, 
                            {{ $taskCounts['completed'] }}
                        ],
                        backgroundColor: [
                            'rgb(250, 204, 21)', // yellow-400
                            'rgb(59, 130, 246)', // blue-500
                            'rgb(34, 197, 94)'   // green-500
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
    @endif
</x-app-layout>
