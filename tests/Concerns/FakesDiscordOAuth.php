<?php

namespace Tests\Concerns;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\TestResponse;

trait FakesDiscordOAuth
{
    /**
     * Drive the real Discord callback while faking outbound Discord HTTP only.
     *
     * @param  array<string, mixed>  $profile
     */
    protected function discordCallback(array $profile, string $code = 'oauth-test-code'): TestResponse
    {
        Http::fake(function (Request $request) use ($profile) {
            $url = $request->url();

            if (str_contains($url, 'https://discord.com/api/oauth2/token')) {
                return Http::response([
                    'access_token' => 'test-access-token',
                    'token_type' => 'Bearer',
                    'expires_in' => 604800,
                    'refresh_token' => 'test-refresh',
                    'scope' => 'identify',
                ], 200);
            }

            if (str_contains($url, 'https://discord.com/api/users/@me')) {
                return Http::response($profile, 200);
            }

            return Http::response(['unexpected' => $url], 404);
        });

        $state = 'state-test-value';

        return $this->withSession(['discord_oauth_state' => $state])
            ->get('/auth/discord/callback?'.http_build_query([
                'code' => $code,
                'state' => $state,
            ]));
    }
}
