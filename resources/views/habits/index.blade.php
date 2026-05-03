<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes habitudes') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Mes habitudes</h1>
            <p class="mt-2 text-sm text-slate-600 max-w-2xl">Suivez vos habitudes quotidiennes et hebdomadaires avec une vue claire et des actions rapides.</p>
        </div>
        <a href="{{ route('habits.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#4A90E2] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600">
            Créer une habitude
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 shadow-sm mb-8">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($habits as $habit)
            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 p-6" style="border-left: 6px solid {{ $habit->color }};">
                    <div class="flex justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-3.5 w-3.5 rounded-full" style="background-color: {{ $habit->color }}"></span>
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">{{ $habit->name }}</h2>
                                <p class="mt-2 text-sm text-slate-500 capitalize">{{ $habit->frequency }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] {{ $habit->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $habit->status }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm leading-6 text-slate-600 mb-6">{{ $habit->description ?? 'Pas de description disponible pour cette habitude.' }}</p>

                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 mb-6">
                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ ucfirst($habit->frequency) }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ $habit->color }}</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('habits.show', $habit) }}" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Voir</a>
                        <a href="{{ route('habits.edit', $habit) }}" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Modifier</a>
                        <form action="{{ route('habits.destroy', $habit) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100" onclick="return confirm('Supprimer cette habitude ?')">Supprimer</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-[20px] border border-dashed border-slate-300 bg-white p-10 text-center text-slate-600 shadow-sm">
                <p class="text-lg font-semibold">Aucune habitude trouvée</p>
                <p class="mt-3 text-sm">Commencez par créer une première habitude pour suivre votre progrès.</p>
                <a href="{{ route('habits.create') }}" class="mt-6 inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Créer une habitude</a>
            </div>
        @endforelse
    </div>
</div>
</x-app-layout>