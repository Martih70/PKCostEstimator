<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Users</h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage user accounts and roles</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add User
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-card">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-500">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-500">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 font-semibold text-xs">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-gray-400">(you)</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @php
                                $roleColors = [
                                    'admin'        => 'bg-slate-100 text-slate-700',
                                    'cost_manager' => 'bg-blue-50 text-blue-700',
                                    'reviewer'     => 'bg-purple-50 text-purple-700',
                                ];
                                $roleLabels = [
                                    'admin'        => 'Admin',
                                    'cost_manager' => 'Cost Manager',
                                    'reviewer'     => 'Reviewer',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-sm text-slate-600 hover:text-slate-900 font-medium transition">
                                    Edit
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium transition">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
