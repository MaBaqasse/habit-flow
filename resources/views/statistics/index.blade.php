<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Statistiques') }}</h2>
                <p class="text-sm text-gray-600">Vue d'ensemble complète de vos habitudes et votre progression.</p>
            </div>
            <form action="{{ route('statistics.export-csv') }}" method="POST" class="mt-2 sm:mt-0">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2m0 0v-8m0 8l-6-4m6 4l6-4"></path>
                    </svg>
                    Exporter en CSV
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Insights Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Total Habits -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Habitudes actives</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $insights['totalHabits'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">À suivre régulièrement</p>
                </div>

                <!-- Week Completions -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Complétions cette semaine</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $insights['weekCompletions'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Actions accomplies</p>
                </div>

                <!-- Month Completions -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Complétions ce mois</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $insights['monthCompletions'] }}</p>
                    <p class="mt-2 text-sm text-slate-500">Progression mensuelle</p>
                </div>

                <!-- Average Completion Rate -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-slate-500">Taux moyen (7j)</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $sevenDaysData['average'] }}%</p>
                    <p class="mt-2 text-sm text-slate-500">Performance globale</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid gap-6 lg:grid-cols-3 mb-8">
                <!-- Line Chart -->
                <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-6 text-lg font-semibold text-slate-900">Taux de complétion (7 derniers jours)</h3>
                    <canvas id="completionChart" height="80"></canvas>
                </div>

                <!-- Donut Chart -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-6 text-lg font-semibold text-slate-900">Distribution</h3>
                    <canvas id="distributionChart" height="240"></canvas>
                </div>
            </div>

            <!-- Text Insights -->
            <div class="grid gap-6 md:grid-cols-2 mb-8">
                <!-- Best Day -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">📅 Votre meilleur jour</h3>
                    <div class="bg-gradient-to-br from-violet-50 to-violet-100 rounded-2xl p-6">
                        <p class="text-3xl font-bold text-violet-900">{{ $insights['bestDay']['name'] }}</p>
                        <p class="text-sm text-violet-700 mt-2">
                            Vous avez {{ $insights['bestDay']['rate'] }}% de réussite ce jour
                        </p>
                    </div>
                </div>

                <!-- Most Stable Habit -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">⭐ Habitude la plus stable</h3>
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-6">
                        <p class="text-3xl font-bold text-emerald-900">{{ $insights['mostStableHabit']['name'] }}</p>
                        <p class="text-sm text-emerald-700 mt-2">
                            {{ $insights['mostStableHabit']['rate'] }}% de taux de complétion
                        </p>
                    </div>
                </div>
            </div>

            <!-- Habits Overview Table -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-6 text-lg font-semibold text-slate-900">Vue d'ensemble des habitudes</h3>

                @if($habits->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Habitude</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Fréquence</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Complétions</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Taux (30j)</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-slate-900">Créée le</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($habits as $habit)
                                    @php
                                        // Calculate completion rate for this habit
                                        $completionsCount = 0;
                                        $totalExpected = 0;
                                        
                                        for ($i = 0; $i < 30; $i++) {
                                            $date = \Carbon\Carbon::now()->subDays($i)->startOfDay();
                                            // Simplified check - in reality use the controller method
                                            if ($date->greaterThanOrEqualTo($habit->created_at->startOfDay())) {
                                                $totalExpected++;
                                                if ($habit->completions->contains(function ($completion) use ($date) {
                                                    return $completion->completed_date->isSameDay($date);
                                                })) {
                                                    $completionsCount++;
                                                }
                                            }
                                        }
                                        
                                        $completionRate = $totalExpected > 0 ? round(($completionsCount / $totalExpected) * 100) : 0;
                                    @endphp
                                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex h-3.5 w-3.5 rounded-full" style="background-color: {{ $habit->color }}"></span>
                                                <span class="text-sm font-medium text-slate-900">{{ $habit->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600">
                                            {{ ucfirst($habit->frequency) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-slate-900">
                                            {{ $habit->completions->count() }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="h-2 w-24 rounded-full bg-slate-100 overflow-hidden">
                                                    <div class="h-full rounded-full transition-all" style="width: {{ $completionRate }}%; background-color: {{ $habit->color }};"></div>
                                                </div>
                                                <span class="text-sm font-medium text-slate-900">{{ $completionRate }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600">
                                            {{ $habit->created_at->translatedFormat('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-12 text-center">
                        <p class="font-semibold text-slate-900">Aucune habitude active</p>
                        <p class="mt-2 text-sm text-slate-500">
                            Créez vos premières habitudes pour voir vos statistiques ici.
                        </p>
                        <a href="{{ route('habits.create') }}" class="mt-4 inline-flex items-center justify-center rounded-2xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">
                            Créer une habitude
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Include Chart.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Prepare data for charts
        const chartLabels = {!! json_encode($sevenDaysData['labels']) !!};
        const completionData = {!! json_encode($sevenDaysData['data']) !!};
        const averageRate = {!! json_encode($sevenDaysData['average']) !!};
        const completed = completionData.filter(rate => rate > 50).length;
        const notCompleted = completionData.filter(rate => rate <= 50).length;

        // Line Chart Configuration
        const lineCtx = document.getElementById('completionChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Taux de complétion %',
                    data: completionData,
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#7c3aed',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    filler: {
                        propagate: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Donut Chart Configuration
        const donutCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Jours réussis (>50%)', 'Jours plus difficiles (≤50%)'],
                datasets: [{
                    data: [completed, notCompleted],
                    backgroundColor: ['#10b981', '#f97316'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</x-app-layout>
