<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Credentials') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-bold">All Credentials</h3>
                        <div class="space-x-2">
                            <a href="{{ route('credentials.export.excel') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Export Excel</a>
                            <a href="{{ route('credentials.export.pdf') }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Export PDF</a>
                            <a href="{{ route('credentials.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Add Credential</a>
                        </div>
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
                                    <th class="p-3 text-sm font-semibold text-gray-700">Name</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Username</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Email</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700">Password</th>
                                    <th class="p-3 text-sm font-semibold text-gray-700 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($credentials as $cred)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-medium text-gray-900">{{ $cred->name }}</td>
                                    <td class="p-3 text-gray-600">{{ $cred->username ?: '-' }}</td>
                                    <td class="p-3 text-gray-600">{{ $cred->email ?: '-' }}</td>
                                    <td class="p-3 text-gray-600">{{ $cred->password }}</td>
                                    <td class="p-3 text-right whitespace-nowrap">
                                        <a href="{{ route('credentials.edit', $cred) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('credentials.destroy', $cred) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this credential?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">No credentials found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
