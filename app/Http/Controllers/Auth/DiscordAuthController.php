<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\DiscordAuthenticator;
use App\Services\DiscordOAuthClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class DiscordAuthController extends Controller
{
    public function redirect(Request $request, DiscordOAuthClient $oauth): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('discord_oauth_state', $state);

        return redirect()->away($oauth->authorizationUrl($state));
    }

    public function callback(
        Request $request,
        DiscordOAuthClient $oauth,
        DiscordAuthenticator $authenticator,
    ): RedirectResponse {
        if ($request->missing('code')) {
            return redirect()
                ->route('login')
                ->withErrors(['discord' => 'Discord login was cancelled.']);
        }

        $expectedState = $request->session()->pull('discord_oauth_state');
        $providedState = $request->string('state')->toString();

        if (! is_string($expectedState) || $expectedState === '' || ! hash_equals($expectedState, $providedState)) {
            abort(403, 'Invalid OAuth state.');
        }

        try {
            $profile = $oauth->userFromCode($request->string('code')->toString());
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->withErrors(['discord' => 'Could not log in with Discord. Try again.']);
        }

        $authenticator->authenticate($profile);
        $request->session()->regenerate();

        return redirect()->intended(route('library'));
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
