<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Departments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-bold">All Departments</h3>
                        <a href="{{ route('departments.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Add Department</a>
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
                                    <th class="p-3 text-sm font-semibold text-gray-700">Name</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $dept)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 text-gray-600">{{ $dept->id }}</td>
                                    <td class="p-3 font-medium text-gray-900">{{ $dept->name }}</td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('departments.edit', $dept) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-gray-500">No departments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
