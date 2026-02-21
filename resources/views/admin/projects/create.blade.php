<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Add New Project</h2>
            <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium px-4 py-2 rounded-lg transition" style="background: white; border: 1px solid #e5e5e5; color: #505b93;">
                Back to Projects
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl rounded-lg p-8" style="background: #fffaf0; border: 1px solid #e5e5e5; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.1);">
        <form method="POST" action="{{ route('admin.projects.store') }}" class="space-y-6">
            @csrf

            <!-- Project ID -->
            <div>
                <label for="project_id" class="block text-sm font-medium text-gray-900 mb-2">Project ID *</label>
                <input type="text" id="project_id" name="project_id" value="{{ old('project_id') }}" required
                    class="w-full rounded-lg px-4 py-2 text-sm focus:outline-none transition-all"
                    style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;"
                    placeholder="e.g., 1701, 1709, 1838">
                @error('project_id')
                    <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Unique ID -->
            <div>
                <label for="unique_id" class="block text-sm font-medium text-gray-900 mb-2">Unique ID *</label>
                <input type="text" id="unique_id" name="unique_id" value="{{ old('unique_id') }}" required
                    class="w-full rounded-lg px-4 py-2 text-sm focus:outline-none transition-all"
                    style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;"
                    placeholder="e.g., PROJ-001, ISB-2024-001">
                @error('unique_id')
                    <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Project Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Project Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg px-4 py-2 text-sm focus:outline-none transition-all"
                    style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;"
                    placeholder="e.g., Community Center Renovation">
                @error('name')
                    <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Project Type -->
            <div>
                <label for="project_type" class="block text-sm font-medium text-gray-900 mb-2">Project Type *</label>
                <select id="project_type" name="project_type" required
                    class="w-full rounded-lg px-4 py-2 text-sm focus:outline-none transition-all"
                    style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;">
                    <option value="">Select project type...</option>
                    <option value="historical" {{ old('project_type') === 'historical' ? 'selected' : '' }}>
                        Historical (Data source for rates)
                    </option>
                    <option value="forecast" {{ old('project_type') === 'forecast' ? 'selected' : '' }}>
                        Forecast (New estimate using rates)
                    </option>
                </select>
                @error('project_type')
                    <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Region -->
            <div>
                <label for="region_id" class="block text-sm font-medium text-gray-900 mb-2">Region *</label>
                <select id="region_id" name="region_id" required
                    class="w-full rounded-lg px-4 py-2 text-sm focus:outline-none transition-all"
                    style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;">
                    <option value="">Select a region...</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>
                @error('region_id')
                    <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget Cost -->
            <div>
                <label for="budget_cost" class="block text-sm font-medium text-gray-900 mb-2">Budget Cost (PKR)</label>
                <input type="number" id="budget_cost" name="budget_cost" value="{{ old('budget_cost') }}" step="0.01" min="0"
                    class="w-full rounded-lg px-4 py-2 text-sm focus:outline-none transition-all"
                    style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;"
                    placeholder="e.g., 5000000">
                @error('budget_cost')
                    <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-900 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="4"
                    class="w-full rounded-lg px-4 py-2 text-sm focus:outline-none transition-all"
                    style="background: white; border: 1px solid #e5e5e5; color: #1b1b18;"
                    placeholder="Add any additional notes about this project...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-sm mt-1" style="color: #ef4444;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex gap-3 pt-6" style="border-top: 1px solid #e5e5e5;">
                <button type="submit" class="flex-1 rounded-lg px-4 py-2 text-sm font-medium text-white transition"
                    style="background: #505b93;">
                    Create Project
                </button>
                <a href="{{ route('admin.projects.index') }}" class="flex-1 rounded-lg px-4 py-2 text-sm font-medium text-center transition"
                    style="background: #f5f5f5; color: #706f6c; border: 1px solid #e5e5e5;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
