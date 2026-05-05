<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $habit->name }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
    <div class="mx-auto max-w-4xl rounded-[24px] border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">{{ $habit->name }}</h1>
                <p class="mt-2 text-sm text-slate-600">Détail complet de l'habitude et son état actuel.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('habits.edit', $habit) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">Modifier</a>
                <form action="{{ route('habits.destroy', $habit) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700" onclick="return confirm('Supprimer cette habitude ?')">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-[20px] border border-slate-200 bg-slate-50 p-6">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Statut</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ $habit->status }}</p>
                    </div>
                    @if($habit->is_active)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold tracking-[0.16em] text-emerald-800">
                            Actif
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold tracking-[0.16em] text-slate-800">
                            Inactif
                        </span>
                    @endif
                </div>

                <dl class="space-y-5 text-sm text-slate-600">
                    <div>
                        <dt class="font-semibold text-slate-800">Fréquence</dt>
                        <dd class="mt-1 capitalize">{{ $habit->frequency }}</dd>
                    </div>
                    <div class="mt-4">
                        <dt class="font-semibold text-slate-800">Catégorie</dt>
                        <dd class="mt-1 capitalize">{{ $habit->category?->name ?? 'Non classée' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-800">Couleur</dt>
                        <dd class="mt-1 flex items-center gap-3">
                            <span class="h-4 w-4 rounded-full" style="background-color: {{ $habit->color }}"></span>
                            <span>{{ $habit->color }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-800">Créée le</dt>
                        <dd class="mt-1">{{ $habit->created_at->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-slate-800">Dernière modification</dt>
                        <dd class="mt-1">{{ $habit->updated_at->format('d M Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-[20px] border border-slate-200 bg-white p-6">
                <h2 class="text-xl font-semibold text-slate-900 mb-4">Description</h2>
                <p class="text-sm leading-7 text-slate-600">{{ $habit->description ?? 'Aucune description fournie pour cette habitude.' }}</p>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('habits.index') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">← Retour à la liste</a>
        </div>
    </div>
</div>
</x-app-layout>