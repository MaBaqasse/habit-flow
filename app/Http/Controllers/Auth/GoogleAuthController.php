<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\GoogleAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/calendar.events',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent',
            ])
            ->redirect();
    }

    public function callback(GoogleAuthService $googleAuthService): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $googleAuthService->storeTokens($googleUser, $user);

        return redirect()->route('dashboard')->with('status', 'Google Calendar synchronisé.');
    }
}
