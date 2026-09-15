<?php

namespace App\Services;

use App\Models\User;
use App\Support\DiscordUserProfile;
use Illuminate\Support\Facades\Auth;

class DiscordAuthenticator
{
    public function authenticate(DiscordUserProfile $profile): User
    {
        $user = User::query()->updateOrCreate(
            ['discord_id' => $profile->id],
            [
                'name' => $profile->name,
                'email' => $profile->email,
                'avatar' => $profile->avatarUrl,
            ],
        );

        if ($user->wasRecentlyCreated || $user->roles()->doesntExist()) {
            $user->assignRole('user');

            if (User::query()->count() === 1) {
                $user->assignRole('owner');
            }
        }

        Auth::login($user, remember: true);

        return $user;
    }
}
