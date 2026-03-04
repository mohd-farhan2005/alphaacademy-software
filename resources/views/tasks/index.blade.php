<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
                        <h3 class="text-lg font-bold">All Tasks</h3>
                        
                        <div class="flex items-center gap-2">
                            <form action="{{ route('tasks.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title..." class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <select name="status" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <button type="submit" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition text-sm">Filter</button>
                                @if(request()->filled('search') || request()->filled('status'))
                                    <a href="{{ route('tasks.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 transition">Clear</a>
                                @endif
                            </form>
                            <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition whitespace-nowrap text-sm">Assign Task</a>
                        </div>
                    </div>
                    
                    @if(session('success'))
                        <div class="mb-4 text-green-700 bg-green-100 border border-green-200 p-3 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse border border-gray-100">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="p-3 text-sm font-semibold text-gray-700">Title</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Assigned To</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Responsible Person</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Assigned By</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Status</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Due Date</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-medium text-gray-900 text-sm">{{ $task->title }}</td>
                                    <td class="p-3 text-gray-600 text-sm">{{ optional($task->assignee)->name ?? '-' }}</td>
                                    <td class="p-3 text-gray-600 text-sm">{{ optional($task->responsiblePerson)->name ?? '-' }}</td>
                                    <td class="p-3 text-gray-600 text-sm">{{ optional($task->assigner)->name ?? '-' }}</td>
                                    <td class="p-3 text-sm">
                                        @if($task->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                        @elseif($task->status === 'in_progress')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">In Progress</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Completed</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-gray-600 text-sm">{{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}</td>
                                    <td class="p-3 text-right text-sm">
                                        @can('update', $task)
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        @endcan
                                        @can('delete', $task)
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-gray-500 text-sm">No tasks found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
