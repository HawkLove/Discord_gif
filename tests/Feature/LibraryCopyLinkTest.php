<?php

namespace Tests\Feature;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LibraryCopyLinkTest extends TestCase
{
    public function test_library_copy_link_button_uses_the_public_media_url(): void
    {
        $user = User::factory()->create();
        $upload = Upload::factory()->for($user)->create([
            'tag' => 'copy-me',
            'visibility' => 'public',
            'media_url' => 'https://media.giphy.com/media/copyme123/giphy.gif',
        ]);

        $this->actingAs($user)
            ->get(route('library'))
            ->assertOk()
            ->assertSee('Copy link', false)
            ->assertSee('data-copy-url="'.$upload->publicMediaUrl().'"', false)
            ->assertSee('/js/app.js', false)
            ->assertDontSee('Send to Discord', false);

        $this->assertFalse(Route::has('uploads.send'));
    }

    public function test_file_upload_copy_link_uses_the_public_file_url(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('uploads.store'), [
                'file' => UploadedFile::fake()->image('loop.gif', 24, 24),
                'tag' => 'file-copy',
                'visibility' => 'public',
            ])
            ->assertRedirect(route('library'));

        $upload = Upload::query()->where('tag', 'file-copy')->firstOrFail();

        $this->actingAs($user)
            ->get(route('library'))
            ->assertOk()
            ->assertSee('data-copy-url="'.$upload->publicMediaUrl().'"', false)
            ->assertSee('Copy link', false);
    }
}
