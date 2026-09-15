<?php

namespace Tests\Feature;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    public function test_giphy_and_file_uploads_store_tag_and_visibility_and_private_is_hidden(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('uploads.store'), [
                'giphy_url' => 'https://giphy.com/gifs/funny-cat-abc123XYZ',
                'tag' => 'public-tag-xyz',
                'visibility' => 'public',
            ])
            ->assertRedirect(route('library'));

        $this->assertDatabaseHas('uploads', [
            'user_id' => $owner->id,
            'tag' => 'public-tag-xyz',
            'visibility' => 'public',
            'source_type' => 'giphy',
            'source_url' => 'https://giphy.com/gifs/funny-cat-abc123XYZ',
            'media_url' => 'https://media.giphy.com/media/abc123XYZ/giphy.gif',
        ]);

        $file = UploadedFile::fake()->image('dance.gif', 24, 24);

        $this->actingAs($owner)
            ->post(route('uploads.store'), [
                'file' => $file,
                'tag' => 'private-tag-abc',
                'visibility' => 'private',
            ])
            ->assertRedirect(route('library'));

        $this->assertDatabaseHas('uploads', [
            'user_id' => $owner->id,
            'tag' => 'private-tag-abc',
            'visibility' => 'private',
            'source_type' => 'file',
        ]);

        $private = Upload::query()->where('tag', 'private-tag-abc')->first();
        $this->assertNotNull($private);
        $this->assertNotEmpty($private->path);
        Storage::disk('public')->assertExists($private->path);

        $public = Upload::query()->where('tag', 'public-tag-xyz')->first();
        $this->assertNotNull($public);

        $stranger = User::factory()->create();
        $listed = $this->actingAs($stranger)
            ->get(route('library'))
            ->assertOk()
            ->viewData('uploads');

        $this->assertTrue($listed->contains('id', $public->id));
        $this->assertFalse($listed->contains('id', $private->id));
        $this->assertTrue($listed->contains('tag', 'public-tag-xyz'));
        $this->assertFalse($listed->contains('tag', 'private-tag-abc'));
    }
}
