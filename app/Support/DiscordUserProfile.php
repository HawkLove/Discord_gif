<?php

namespace App\Support;

class DiscordUserProfile
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $avatarUrl,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        $id = (string) $data['id'];
        $name = (string) ($data['global_name'] ?: $data['username'] ?: 'Discord User');
        $avatarHash = $data['avatar'] ?? null;

        return new self(
            id: $id,
            name: $name,
            email: isset($data['email']) ? (string) $data['email'] : null,
            avatarUrl: is_string($avatarHash) && $avatarHash !== ''
                ? "https://cdn.discordapp.com/avatars/{$id}/{$avatarHash}.png"
                : null,
        );
    }
}
