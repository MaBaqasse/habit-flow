<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $habit->name }}</h2>
                <p class="text-sm text-gray-600 mt-1">Statistiques et insights détaillés</p>
            </div>
            <div class="flex gap-2">
                <span class="inline-flex h-4 w-4 rounded-full mt-1" style="background-color: {{ $habit->color }}"></span>
                <span class="text-sm font-medium text-slate-500">{{ ucfirst($habit->frequency) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- KPI Cards -->
            <div class="grid gap-4 xl:grid-cols-4 mb-8">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Série Actuelle</p>
                    <p class="mt-3 text-3xl font-semibold" style="color: {{ $habit->color }}">{{ optional($habit->streak)->current_streak ?? 0 }}</p>
                    <p class="mt-2 text-xs text-slate-400">jours consécutifs</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Record Historique</p>
                    <p class="mt-3 text-3xl font-semibold" style="color: {{ $habit->color }}">{{ optional($habit->streak)->best_streak ?? 0 }}</p>
                    <p class="mt-2 text-xs text-slate-400">meilleure série</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Complétions (30j)</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $totalCompletionsLast30 }}</p>
                    <p class="mt-2 text-xs text-slate-400">derniers 30 jours</p>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Score Stabilité</p>
                    <p class="mt-3 text-3xl font-semibold" style="color: {{ $habit->color }}">{{ $stabilityScore }}/10</p>
                    <p class="mt-2 text-xs text-slate-400">régularité des heures</p>
                </div>
            </div>

            <!-- Taux de complétion 30j -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Taux de Complétion (30 jours)</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $totalCompletionsLast30 }} complétions sur {{ ceil(30 / ($habit->frequency === 'daily' ? 1 : ($habit->frequency === 'weekly' ? 7 : 30))) }} attendues</p>
                    </div>
                    <div class="text-right">
                        <p class="text-4xl font-bold" style="color: {{ $habit->color }}">{{ $completionRateLast30 }}%</p>
                    </div>
                </div>
                <div class="mt-6 w-full bg-slate-100 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all" style="background-color: {{ $habit->color }}; width: {{ $completionRateLast30 }}%"></div>
                </div>
            </div>

            <!-- Heatmap -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm mb-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-6">
                    @if($habit->frequency === 'daily')
                        Activité (30 derniers jours)
                    @else
                        Activité (12 dernières semaines)
                    @endif
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($heatmapData as $item)
                        <div class="flex flex-col items-center">
                            <div class="h-8 w-8 rounded-lg mb-1 transition-all hover:scale-110 cursor-default"
                                style="background-color: {{ $item['completed'] ? $habit->color : '#e2e8f0' }}"
                                title="{{ $item['date'] }}">
                            </div>
                            <span class="text-xs text-slate-400">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex items-center justify-end gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-sm" style="background-color: #e2e8f0"></div>
                        <span class="text-slate-500">Non fait</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-sm" style="background-color: {{ $habit->color }}"></div>
                        <span class="text-slate-500">Complété</span>
                    </div>
                </div>
            </div>

            <!-- Graphique par semaine -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm mb-8">
                <h3 class="text-lg font-semibold text-slate-900 mb-6">Complétions par Semaine</h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>

            <!-- Informations -->
            <div class="grid gap-6 xl:grid-cols-2 mb-8">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h4 class="font-semibold text-slate-900 mb-4">Détails</h4>
                    <dl class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-600">Fréquence</dt>
                            <dd class="font-semibold text-slate-900 capitalize">{{ $habit->frequency }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-600">Catégorie</dt>
                            <dd class="font-semibold text-slate-900">{{ $habit->category?->name ?? 'Sans catégorie' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-600">Créée le</dt>
                            <dd class="font-semibold text-slate-900">{{ $habit->created_at->format('d M Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-600">Statut</dt>
                            <dd class="font-semibold">
                                @if($habit->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Actif</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-800">Inactif</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h4 class="font-semibold text-slate-900 mb-4">Description</h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $habit->description ?? 'Aucune description fournie.' }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('habits.edit', $habit) }}" class="inline-flex items-center justify-center rounded-full bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                    Modifier
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Retour au dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const weeklyChartCanvas = document.getElementById('weeklyChart');
        const weeklyChart = new Chart(weeklyChartCanvas, {
            type: 'bar',
            data: {
                labels: {!! json_encode($weeklyChartData['labels']) !!},
                datasets: [{
                    label: 'Complétions',
                    data: {!! json_encode($weeklyChartData['data']) !!},
                    backgroundColor: '{{ $habit->color }}',
                    borderColor: '{{ $habit->color }}',
                    borderWidth: 0,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: {{ $habit->frequency === 'daily' ? 7 : 2 }},
                        ticks: {
                            stepSize: 1,
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
