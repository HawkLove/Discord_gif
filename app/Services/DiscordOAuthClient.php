<?php

namespace App\Services;

use App\Support\DiscordUserProfile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DiscordOAuthClient
{
    public function authorizationUrl(string $state): string
    {
        return 'https://discord.com/oauth2/authorize?'.http_build_query([
            'client_id' => config('services.discord.client_id'),
            'redirect_uri' => config('services.discord.redirect'),
            'response_type' => 'code',
            'scope' => 'identify',
            'state' => $state,
            'prompt' => 'consent',
        ]);
    }

    public function userFromCode(string $code): DiscordUserProfile
    {
        $tokenResponse = Http::asForm()
            ->acceptJson()
            ->post('https://discord.com/api/oauth2/token', [
                'client_id' => config('services.discord.client_id'),
                'client_secret' => config('services.discord.client_secret'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.discord.redirect'),
            ]);

        if ($tokenResponse->failed()) {
            throw new RuntimeException('Discord token exchange failed.');
        }

        $accessToken = $tokenResponse->json('access_token');

        if (! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('Discord token response was missing an access token.');
        }

        $profileResponse = Http::withToken($accessToken)
            ->acceptJson()
            ->get('https://discord.com/api/users/@me');

        if ($profileResponse->failed()) {
            throw new RuntimeException('Discord profile lookup failed.');
        }

        /** @var array<string, mixed> $profile */
        $profile = $profileResponse->json();

        return DiscordUserProfile::fromApi($profile);
    }
}
