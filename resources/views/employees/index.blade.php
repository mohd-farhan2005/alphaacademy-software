<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
                        <h3 class="text-lg font-bold">All Employees</h3>
                        
                        <div class="flex items-center gap-2">
                            <form action="{{ route('employees.index') }}" method="GET" class="flex gap-2">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <button type="submit" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition text-sm">Search</button>
                                @if(request()->filled('search'))
                                    <a href="{{ route('employees.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 transition">Clear</a>
                                @endif
                            </form>
                            <a href="{{ route('employees.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition whitespace-nowrap text-sm">Add Employee</a>
                        </div>
                    </div>
                    
                    @if(session('success'))
                        <div class="mb-4 text-green-700 bg-green-100 border border-green-200 p-3 rounded text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="p-3 text-sm font-semibold text-gray-700">Name</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Email</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Role</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Department</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $emp)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-medium text-gray-900 text-sm">{{ $emp->name }}</td>
                                    <td class="p-3 text-gray-600 text-sm">{{ $emp->email }}</td>
                                    <td class="p-3 text-gray-600 text-sm">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ str_replace('_', ' ', strtoupper($emp->role->value ?? '')) }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-gray-600 text-sm">{{ $emp->department ? $emp->department->name : '-' }}</td>
                                    <td class="p-3 text-right text-sm">
                                        @can('view', $emp)
                                            <a href="{{ route('employees.show', $emp) }}" class="text-green-600 hover:text-green-900 mr-3" title="View Performance">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </a>
                                        @endcan
                                        @can('update', $emp)
                                            <a href="{{ route('employees.edit', $emp) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        @endcan
                                        @can('delete', $emp)
                                            <form action="{{ route('employees.destroy', $emp) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500 text-sm">No employees found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
