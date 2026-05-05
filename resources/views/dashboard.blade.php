<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
                <p class="text-sm text-gray-600">Vue d'ensemble de vos habitudes, streaks et catégories.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 xl:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Taux de complétion aujourd'hui</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $dailyCompletionRate }}%</p>
                    <p class="mt-2 text-sm text-slate-500">{{ $completedToday }} habitude(s) complétée(s) aujourd'hui</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Habitudes actives</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $habits->count() }}</p>
                    <p class="mt-2 text-sm text-slate-500">Selon le filtre sélectionné</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Catégorie sélectionnée</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900">{{ $selectedCategory ? ($categories->firstWhere('id', $selectedCategory)?->name ?? 'Inconnue') : 'Toutes' }}</p>
                    <p class="mt-2 text-sm text-slate-500">Filtrer les habitudes par catégorie</p>
                </div>
            </div>

            <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            {{-- On garde la structure flex pour l'alignement --}}
            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex flex-col gap-2 md:w-1/2">
                    <label for="category" class="text-sm font-medium text-slate-700">Filtrer par catégorie</label>
                    <select id="category" name="category" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-100">
                        <option value="">Toutes les catégories</option>
                        {{-- Utilisation de la logique de sélection basée sur la requête --}}
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
            </div>

        <div class="flex items-center gap-3">
            {{-- Le bouton "Filtrer" (ici "Appliquer") --}}
            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">
                Appliquer
            </button>
            {{-- Bouton pour réinitialiser les filtres --}}
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Réinitialiser
            </a>
             </div>
            </form>
        </div>

            <div class="mt-8 grid gap-6 xl:grid-cols-3">
                @forelse($habits as $habit)
                    <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 p-6" style="{{ 'border-left: 6px solid ' . $habit->color . ';' }}">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-3.5 w-3.5 rounded-full" style="{{ 'background-color: ' . $habit->color . ';' }}"></span>
                                        <h3 class="text-xl font-semibold text-slate-900">{{ $habit->name }}</h3>
                                    </div>
                                    <p class="mt-3 text-sm text-slate-500">{{ $habit->description ?? 'Aucune description fournie.' }}</p>
                                </div>

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-slate-700">{{ ucfirst($habit->frequency) }}</span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="mb-5 flex flex-wrap items-center gap-2 text-sm text-slate-600">
                                <span class="rounded-full bg-slate-100 px-3 py-1">{{ $habit->category?->name ?? 'Sans catégorie' }}</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1">Couleur : {{ $habit->color }}</span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-3xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Série actuelle</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ optional($habit->streak)->current_streak ?? 0 }}</p>
                                </div>
                                <div class="rounded-3xl bg-slate-50 px-4 py-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Meilleure série</p>
                                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ optional($habit->streak)->best_streak ?? 0 }}</p>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <form action="{{ route('habits.complete', $habit) }}" method="POST" class="inline-flex">
                                    @csrf
                                    <x-check-in-button>Check-in</x-check-in-button>
                                </form>
                                <a href="{{ route('habits.edit', $habit) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Modifier</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-700 shadow-sm">
                        <p class="text-lg font-semibold">Aucune habitude trouvée.</p>
                        <p class="mt-3 text-sm text-slate-500">Sélectionnez une autre catégorie ou ajoutez une nouvelle habitude pour commencer.</p>
                        <a href="{{ route('habits.create') }}" class="mt-6 inline-flex items-center justify-center rounded-full bg-violet-600 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-700">Créer une habitude</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
