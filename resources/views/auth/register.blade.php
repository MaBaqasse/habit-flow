<x-guest-layout>
    <div class="mb-2">
        <h1 class="text-2xl font-bold text-text-primary text-center">{{ __('Create Account') }}</h1>
        <p class="text-sm text-text-secondary text-center mt-1">{{ __('Start organizing your habits today') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6 mt-8">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-2 w-full px-4 py-2.5 border" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-2 w-full px-4 py-2.5 border" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-2 w-full px-4 py-2.5 border"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-2 w-full px-4 py-2.5 border"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col-reverse gap-3 pt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Create Account') }}
            </x-primary-button>

            <div class="text-center">
                <span class="text-sm text-text-secondary">{{ __('Already have an account?') }}</span>
                <a class="text-sm font-semibold text-brand-primary hover:text-blue-600 transition" href="{{ route('login') }}">
                    {{ __('Sign in') }}
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
