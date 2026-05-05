<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une habitude') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
    <div class="mx-auto max-w-3xl rounded-[24px] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Créer une habitude</h1>
            <p class="mt-2 text-sm text-slate-600">Ajoutez une nouvelle habitude et commencez à suivre votre progression.</p>
        </div>

        <form action="{{ route('habits.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid gap-6">
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nom de l'habitude *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('name') border-red-400 bg-red-50 @enderror"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 @error('description') border-red-400 bg-red-50 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Champ Catégorie -->
                <div class="mt-4">
                    <label for="category_id" class="block font-medium text-sm text-gray-700">Catégorie</label>
                    <select name="category_id" id="category_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                        <option value="">-- Choisir une catégorie --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ isset($habit) && $habit->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="frequency" class="mb-2 block text-sm font-semibold text-slate-700">Fréquence *</label>
                        <select name="frequency" id="frequency"
                                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#4A90E2] focus:ring-2 focus:ring-blue-100 @error('frequency') border-red-400 bg-red-50 @enderror"
                                required>
                            <option value="">Choisir</option>
                            <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Quotidienne</option>
                            <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                        </select>
                        @error('frequency')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="color" class="mb-2 block text-sm font-semibold text-slate-700">Couleur *</label>
                        <input type="color" name="color" id="color" value="{{ old('color', '#4A90E2') }}"
                               class="h-14 w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#4A90E2] focus:ring-2 focus:ring-blue-100 @error('color') border-red-400 bg-red-50 @enderror"
                               required>
                        @error('color')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-4">
                    <label class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-5 w-5 rounded border-slate-300 text-[#4A90E2] focus:ring-[#4A90E2]">
                        Actif
                    </label>
                    <p class="text-sm text-slate-500">Désactivez l'habitude si elle n'est plus pertinente.</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:justify-end">
                <a href="{{ route('habits.index') }}" class="inline-flex justify-center rounded-lg border border-slate-200 bg-slate-50 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Annuler</a>
                <button type="submit" class="inline-flex justify-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Créer</button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>