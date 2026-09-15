<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->bothify('tag-##??');

        return [
            'name' => $name,
            'slug' => Str::slug($name) ?: $name,
            'user_id' => null,
        ];
    }

    public function personal(?User $user = null): static
    {
        return $this->for($user ?? User::factory());
    }
}
