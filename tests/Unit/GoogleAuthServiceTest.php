<?php

use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('stores google tokens securely for the user', function () {
    $user = User::factory()->create();
    $service = new GoogleAuthService;

    $socialiteUser = (object) [
        'id' => 'google-123',
        'token' => 'access-token',
        'refreshToken' => 'refresh-token',
        'expiresIn' => 3600,
    ];

    $service->storeTokens($socialiteUser, $user);

    $user->refresh();

    expect($user->google_id)->toBe('google-123');
    expect($user->google_access_token)->toBe('access-token');
    expect($user->google_refresh_token)->toBe('refresh-token');
    expect($user->google_calendar_sync_enabled)->toBeTrue();
    expect($user->google_token_expires_at)->toBeInstanceOf(Carbon::class);
});

it('returns the current token when it is still valid', function () {
    $user = User::factory()->create([
        'google_access_token' => 'current-token',
        'google_token_expires_at' => now()->addMinutes(30),
    ]);

    $service = new GoogleAuthService;

    expect($service->getValidToken($user))->toBe('current-token');
});

it('refreshes the access token when it is expired', function () {
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response([
            'access_token' => 'new-access-token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $user = User::factory()->create([
        'google_access_token' => 'old-access-token',
        'google_refresh_token' => 'refresh-token',
        'google_token_expires_at' => now()->subMinutes(10),
    ]);

    $service = new GoogleAuthService;
    $token = $service->getValidToken($user);

    expect($token)->toBe('new-access-token');
    expect($user->refresh()->google_access_token)->toBe('new-access-token');
});

it('disables sync when refresh fails', function () {
    Http::fake([
        'oauth2.googleapis.com/token' => Http::response(['error' => 'invalid_grant'], 400),
    ]);

    $user = User::factory()->create([
        'google_access_token' => 'old-access-token',
        'google_refresh_token' => 'refresh-token',
        'google_token_expires_at' => now()->subMinutes(10),
        'google_calendar_sync_enabled' => true,
    ]);

    $service = new GoogleAuthService;
    $token = $service->getValidToken($user);

    expect($token)->toBeNull();
    expect($user->refresh()->google_calendar_sync_enabled)->toBeFalse();
});
