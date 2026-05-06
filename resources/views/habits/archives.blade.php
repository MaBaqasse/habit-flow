<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Habitats archivées') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Mes habitudes archivées</h1>
                <p class="mt-2 text-sm text-slate-600 max-w-2xl">Retrouvez les habitudes désactivées pour les réactiver sans perdre leur historique.</p>
            </div>
            <a href="{{ route('habits.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">
                Retour aux habitudes actives
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 shadow-sm mb-8">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse($archivedHabits as $habit)
                <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm opacity-75">
                    <div class="border-b border-slate-100 p-6" style="border-left: 6px solid {{ $habit->color }};">
                        <div class="flex justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-3.5 w-3.5 rounded-full" style="background-color: {{ $habit->color }}"></span>
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-900">{{ $habit->name }}</h2>
                                    <p class="mt-2 text-sm text-slate-500 capitalize">{{ $habit->frequency }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] bg-slate-100 text-slate-600">
                                {{ $habit->status }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-sm leading-6 text-slate-600 mb-4">{{ $habit->description ?? 'Pas de description disponible pour cette habitude.' }}</p>

                        <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 mb-6">
                            <span class="rounded-full bg-slate-100 px-3 py-1">{{ ucfirst($habit->frequency) }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1">{{ $habit->color }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1">{{ $habit->category?->name ?? 'Sans catégorie' }}</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1">Depuis {{ $habit->updated_at->diffForHumans() }}</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('habits.show', $habit) }}" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Voir</a>
                            <a href="{{ route('habits.edit', $habit) }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Modifier</a>
                            <form action="{{ route('habits.update', $habit) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="1">
                                <button type="submit" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                    Réactiver
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-[20px] border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600 shadow-sm">
                    <p class="text-lg font-semibold">Aucune habitude archivée</p>
                    <p class="mt-3 text-sm">Les habitudes archivées n'apparaissent pas ici tant qu'elles ne sont pas désactivées.</p>
                    <a href="{{ route('habits.index') }}" class="mt-6 inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Voir les habitudes actives</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
