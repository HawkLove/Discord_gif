<?php

namespace App\Services;

use App\Models\Upload;
use App\Models\User;
use App\Support\GiphyUrl;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadStore
{
    /**
     * @param  array{tag_id: int, visibility: string, giphy_url?: string|null, file?: UploadedFile|null}  $data
     */
    public function store(User $user, array $data): Upload
    {
        if (($data['file'] ?? null) instanceof UploadedFile) {
            return $this->storeFile($user, $data);
        }

        $sourceUrl = (string) $data['giphy_url'];

        return $user->uploads()->create([
            'tag_id' => $data['tag_id'],
            'visibility' => $data['visibility'],
            'source_type' => 'giphy',
            'source_url' => $sourceUrl,
            'media_url' => GiphyUrl::mediaUrl($sourceUrl),
            'path' => null,
            'mime_type' => 'image/gif',
            'original_filename' => null,
        ]);
    }

    /**
     * @param  array{tag_id: int, visibility: string, file: UploadedFile}  $data
     */
    protected function storeFile(User $user, array $data): Upload
    {
        $file = $data['file'];
        $path = $file->store('uploads/'.$user->id, 'public');

        return $user->uploads()->create([
            'tag_id' => $data['tag_id'],
            'visibility' => $data['visibility'],
            'source_type' => 'file',
            'source_url' => null,
            'media_url' => Storage::disk('public')->url($path),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'original_filename' => $file->getClientOriginalName(),
        ]);
    }
}
