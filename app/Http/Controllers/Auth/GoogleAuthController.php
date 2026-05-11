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
                'https://www.googleapis.com/auth/calendar',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent',
            ])
            ->redirect();
    }

    public function callback(GoogleAuthService $googleAuthService): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Erreur d\'authentification Google.');
        }

        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Stockage sécurisé des tokens via votre Service
        $googleAuthService->storeTokens($googleUser, $user);

        // Redirection vers la page de profil
        return redirect()->route('profile.edit')->with('status', 'Google Calendar connecté avec succès.');
    }

    /**
     * Implémentation de la déconnexion Google
     */
    public function disconnect(): RedirectResponse
    {
        $user = Auth::user();

        $user->update([
            'google_id' => null,
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires_at' => null,
            'google_calendar_sync_enabled' => false,
        ]);

        return redirect()->back()->with('status', 'Synchronisation Google Calendar désactivée.');
    }
}
