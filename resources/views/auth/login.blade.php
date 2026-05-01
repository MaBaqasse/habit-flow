<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-2">
        <h1 class="text-2xl font-bold text-text-primary text-center">{{ __('Welcome Back') }}</h1>
        <p class="text-sm text-text-secondary text-center mt-1">{{ __('Sign in to your account') }}</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6 mt-8">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-2 w-full px-4 py-2.5 border" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-2 w-full px-4 py-2.5 border"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-bg-light text-brand-primary focus:ring-brand-primary focus:ring-1 shadow-sm" name="remember">
                <span class="ms-2 text-sm text-text-secondary font-medium">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col-reverse gap-3 pt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Sign in') }}
            </x-primary-button>

            @if (Route::has('password.request'))
                <div class="text-center">
                    <a class="text-sm font-semibold text-brand-primary hover:text-blue-600 transition" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>
            @endif
        </div>

        <div class="pt-2 border-t border-bg-light text-center">
            <span class="text-sm text-text-secondary">{{ __("Don't have an account?") }}</span>
            <a class="text-sm font-semibold text-brand-primary hover:text-blue-600 transition" href="{{ route('register') }}">
                {{ __('Create one') }}
            </a>
        </div>
    </form>
</x-guest-layout>
