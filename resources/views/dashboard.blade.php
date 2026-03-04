<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-1">Welcome, {{ Auth::user()->name }}!</h3>
                    <p>You are logged in as a <strong>{{ str_replace('_', ' ', strtoupper($role->value)) }}</strong>.</p>
                </div>
            </div>

            @isset($data['monthly_datasets'])
            {{-- Month-wise Line Chart --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-1">📈 Monthly Tasks Report — {{ $data['chart_year'] }}</h3>
                    <p class="text-sm text-gray-500 mb-4">Tasks created per month, grouped by department (category)</p>
                    <div style="position:relative; height:340px;">
                        <canvas id="monthlyLineChart"></canvas>
                    </div>
                </div>
            </div>
            @endisset

            @isset($data['dept_labels'])
            {{-- Department Bar + Doughnut Charts --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-1">📊 Tasks by Department (Category Report)</h3>
                    <p class="text-sm text-gray-500 mb-4">Total tasks assigned per department</p>
                    <div class="flex flex-col lg:flex-row gap-6 items-center">
                        <div class="w-full lg:w-1/2" style="max-height:320px;">
                            <canvas id="deptBarChart"></canvas>
                        </div>
                        <div class="w-full lg:w-1/2" style="max-height:320px;">
                            <canvas id="deptDoughnutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endisset

            <!-- Stat Number Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @isset($data['departments_count'])
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Departments</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $data['departments_count'] }}</div>
                </div>
                @endisset

                @isset($data['employees_count'])
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Employees Overview</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $data['employees_count'] }}</div>
                </div>
                @endisset

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Tasks</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $data['total_tasks'] ?? 0 }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Completed Tasks</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $data['completed_tasks'] ?? 0 }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">Pending Tasks</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $data['pending_tasks'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Recent Tasks Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Tasks</h3>
                        <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 border border-gray-300 rounded-md transition text-sm font-medium whitespace-nowrap">
                            View All Tasks
                        </a>
                    </div>
                    @if(isset($data['recent_tasks']) && $data['recent_tasks']->count() > 0)
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
                                @foreach($data['recent_tasks'] as $task)
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
                                        <a href="{{ route('tasks.index') }}?search={{ urlencode($task->title) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
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

    {{-- Chart.js (loaded once) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @isset($data['monthly_datasets'])
    <script>
        (function() {
            const monthLabels   = @json($data['monthly_month_names']);
            const monthDatasets = @json($data['monthly_datasets']);
            new Chart(document.getElementById('monthlyLineChart'), {
                type: 'line',
                data: { labels: monthLabels, datasets: monthDatasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, padding: 16, font: { size: 12 } } },
                        tooltip: { mode: 'index' }
                    },
                    scales: {
                        x: { grid: { color: '#f0f0f0' }, ticks: { font: { size: 12 } } },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f0f0' },
                            ticks: { stepSize: 1, font: { size: 12 } },
                            title: { display: true, text: 'Tasks', font: { size: 13 } }
                        }
                    }
                }
            });
        })();
    </script>
    @endisset

    @isset($data['dept_labels'])
    <script>
        (function() {
            const deptLabels = @json($data['dept_labels']);
            const deptCounts = @json($data['dept_task_counts']);
            const palette  = ['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316','#84cc16'];
            // Assign yellow (#eab308) to DME departments
            const colors   = deptLabels.map((lbl, i) => lbl.toLowerCase().includes('dme') ? '#eab308' : palette[i % palette.length]);
            const bgColors = colors.map(c => c + 'cc');

            new Chart(document.getElementById('deptBarChart'), {
                type: 'bar',
                data: {
                    labels: deptLabels,
                    datasets: [{ label: 'Tasks Assigned', data: deptCounts, backgroundColor: bgColors, borderColor: colors, borderWidth: 2, borderRadius: 6 }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: 'Tasks per Department', font: { size: 14 } }
                    },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });

            new Chart(document.getElementById('deptDoughnutChart'), {
                type: 'doughnut',
                data: {
                    labels: deptLabels,
                    datasets: [{ data: deptCounts, backgroundColor: bgColors, borderColor: colors, borderWidth: 2 }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right' },
                        title: { display: true, text: 'Distribution by Department', font: { size: 14 } }
                    }
                }
            });
        })();
    </script>
    @endisset

</x-app-layout>
