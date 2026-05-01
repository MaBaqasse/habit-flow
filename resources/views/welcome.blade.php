<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-[#2D3436] min-h-screen flex flex-col">
        <!-- Header -->
        <header class="w-full px-6 py-4 lg:px-8 lg:py-6">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-[#4A90E2] rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">H</span>
                    </div>
                    <h1 class="text-xl font-semibold text-[#2D3436]">HabitFlow</h1>
                </div>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a
                                href="{{ url('/dashboard') }}"
                                class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg font-medium hover:bg-[#357ABD] transition-colors"
                            >
                                Dashboard
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="px-4 py-2 text-[#636E72] hover:text-[#2D3436] font-medium transition-colors"
                            >
                                Se connecter
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="px-4 py-2 bg-[#4A90E2] text-white rounded-lg font-medium hover:bg-[#357ABD] transition-colors"
                                >
                                    S'inscrire
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-6 py-12 lg:px-8">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Hero Section -->
                <div class="mb-16">
                    <h2 class="text-4xl lg:text-5xl font-bold text-[#2D3436] mb-6 leading-tight">
                        Transformez vos habitudes,<br>
                        <span class="text-[#4A90E2]">un jour à la fois</span>
                    </h2>

                    <p class="text-lg text-[#636E72] mb-8 max-w-2xl mx-auto leading-relaxed">
                        HabitFlow vous aide à créer, suivre et analyser vos habitudes quotidiennes.
                        Avec des streaks motivants, des statistiques détaillées et des rappels personnalisés,
                        atteignez vos objectifs de manière durable.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="px-8 py-3 bg-[#4A90E2] text-white rounded-xl font-semibold text-lg hover:bg-[#357ABD] transition-colors shadow-lg hover:shadow-xl"
                            >
                                Commencer maintenant
                            </a>
                        @endif

                        <a
                            href="#features"
                            class="px-8 py-3 border-2 border-[#4A90E2] text-[#4A90E2] rounded-xl font-semibold text-lg hover:bg-[#4A90E2] hover:text-white transition-colors"
                        >
                            En savoir plus
                        </a>
                    </div>
                </div>

                <!-- Features Section -->
                <div id="features" class="grid md:grid-cols-3 gap-8 mb-16">
                    <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-[#2ECC71] rounded-full flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#2D3436] mb-2">Suivi Quotidien</h3>
                        <p class="text-[#636E72]">Cochez vos habitudes chaque jour et visualisez votre progression en temps réel.</p>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-[#F5A623] rounded-full flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#2D3436] mb-2">Streaks & Stats</h3>
                        <p class="text-[#636E72]">Gardez la motivation avec vos séries consécutives et des graphiques détaillés.</p>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-[#FF5E5E] rounded-full flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-[#2D3436] mb-2">Rappels Intelligents</h3>
                        <p class="text-[#636E72]">Recevez des notifications email pour ne jamais oublier vos habitudes.</p>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="bg-[#4A90E2] rounded-3xl p-8 lg:p-12 text-white">
                    <h3 class="text-2xl lg:text-3xl font-bold mb-4">
                        Prêt à changer vos habitudes ?
                    </h3>
                    <p class="text-lg mb-6 opacity-90">
                        Rejoignez des milliers d'utilisateurs qui transforment leur quotidien avec HabitFlow.
                    </p>

                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="inline-block px-8 py-3 bg-white text-[#4A90E2] rounded-xl font-semibold text-lg hover:bg-gray-100 transition-colors"
                        >
                            Créer mon compte gratuit
                        </a>
                    @endif
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="px-6 py-8 lg:px-8 border-t border-gray-200">
            <div class="max-w-7xl mx-auto text-center">
                <p class="text-[#636E72] text-sm">
                    © 2026 HabitFlow. Développé avec ❤️ pour vous aider à atteindre vos objectifs.
                </p>
            </div>
        </footer>
    </body>
</html>
