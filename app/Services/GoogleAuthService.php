<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class GoogleAuthService
{
    /**
     * Enregistre les tokens initiaux reçus de Socialite.
     */
    public function storeTokens(object $socialiteUser, User $user): void
    {
        $user->update([
            'google_id' => $socialiteUser->id,
            'google_access_token' => $socialiteUser->token,
            'google_refresh_token' => $socialiteUser->refreshToken,
            'google_token_expires_at' => now()->addSeconds($socialiteUser->expiresIn),
            'google_calendar_sync_enabled' => true,
        ]);
    }

    /**
     * Retourne un access token valide pour l'utilisateur.
     */
    public function getValidToken(User $user): ?string
    {
        if (! $user->google_token_expires_at) {
            return null;
        }

        if ($user->google_token_expires_at->copy()->subMinutes(5)->isPast()) {
            return $this->refreshToken($user);
        }

        return $user->google_access_token;
    }

    /**
     * Rafraîchit le token d'accès Google en utilisant le refresh token.
     */
    protected function refreshToken(User $user): ?string
    {
        if (! $user->google_refresh_token) {
            $user->update(['google_calendar_sync_enabled' => false]);

            return null;
        }

        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $user->google_refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if (! $response->successful()) {
            $user->update(['google_calendar_sync_enabled' => false]);

            return null;
        }

        $data = $response->json();

        $user->update([
            'google_access_token' => $data['access_token'],
            'google_token_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return $data['access_token'];
    }
}
