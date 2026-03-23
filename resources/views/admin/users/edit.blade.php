<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Edit User</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $user->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-lg">
        <div class="card-static px-6 py-6">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200 transition" />
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200 transition" />
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Role</label>
                    <select name="role" required
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200 transition">
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin — Full access</option>
                        <option value="cost_manager" {{ old('role', $user->role) === 'cost_manager' ? 'selected' : '' }}>Cost Manager — Estimator & projects</option>
                        <option value="reviewer" {{ old('role', $user->role) === 'reviewer' ? 'selected' : '' }}>Reviewer — Analytics only</option>
                    </select>
                    @error('role')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">Change Password <span class="normal-case font-normal text-gray-400">(leave blank to keep current)</span></p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">New Password</label>
                            <input type="password" name="password" autocomplete="new-password"
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200 transition" />
                            @error('password')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-200 transition" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 transition">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
