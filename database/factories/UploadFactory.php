<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Upload>
 */
class UploadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = fake()->bothify('??????????');

        return [
            'user_id' => User::factory(),
            'tag_id' => Tag::factory(),
            'visibility' => 'public',
            'source_type' => 'giphy',
            'source_url' => 'https://giphy.com/gifs/example-'.$id,
            'media_url' => 'https://media.giphy.com/media/'.$id.'/giphy.gif',
            'path' => null,
            'mime_type' => 'image/gif',
            'original_filename' => null,
        ];
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'private',
        ]);
    }
}
