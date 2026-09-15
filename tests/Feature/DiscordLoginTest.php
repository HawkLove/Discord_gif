<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\FakesDiscordOAuth;
use Tests\TestCase;

class DiscordLoginTest extends TestCase
{
    use FakesDiscordOAuth;

    /**
     * @return array<string, mixed>
     */
    protected function aliceProfile(): array
    {
        return [
            'id' => 'snowflake-42',
            'username' => 'alice',
            'global_name' => 'Alice',
            'email' => 'alice@example.com',
            'avatar' => 'avhash',
        ];
    }

    public function test_discord_login_redirects_to_discord_authorize_with_identify_scope(): void
    {
        $response = $this->get(route('auth.discord'));

        $response->assertRedirect();
        $location = (string) $response->headers->get('Location');

        $this->assertStringStartsWith('https://discord.com/oauth2/authorize?', $location);
        $this->assertStringContainsString('client_id=', $location);
        $this->assertStringContainsString(rawurlencode('http://localhost/auth/discord/callback'), $location);
        $this->assertTrue(
            str_contains($location, 'scope=identify') || str_contains($location, 'scope='.rawurlencode('identify'))
        );
        $this->assertStringNotContainsString('email', $location);
    }

    public function test_first_discord_callback_creates_a_user_and_authenticates(): void
    {
        $response = $this->discordCallback($this->aliceProfile());

        $response->assertRedirect(route('library'));
        $this->assertAuthenticated();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'discord_id' => 'snowflake-42',
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $user = User::query()->where('discord_id', 'snowflake-42')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasRole('user'));
        $this->assertTrue($user->hasRole('owner'));
    }

    public function test_second_discord_callback_reuses_the_same_user_and_authenticates(): void
    {
        $this->discordCallback($this->aliceProfile())->assertRedirect(route('library'));
        $userId = User::query()->where('discord_id', 'snowflake-42')->value('id');
        $this->assertNotNull($userId);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();

        $this->discordCallback($this->aliceProfile())->assertRedirect(route('library'));

        $this->assertAuthenticated();
        $this->assertDatabaseCount('users', 1);
        $this->assertSame($userId, User::query()->where('discord_id', 'snowflake-42')->value('id'));
        $this->assertAuthenticatedAs(User::query()->findOrFail($userId));
    }
}
