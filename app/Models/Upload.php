<?php

namespace App\Models;

use Database\Factories\UploadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'tag_id',
    'visibility',
    'source_type',
    'source_url',
    'media_url',
    'path',
    'mime_type',
    'original_filename',
])]
class Upload extends Model
{
    /** @use HasFactory<UploadFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Tag, $this>
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isVisibleTo(User $user): bool
    {
        if ($this->isPublic() || $this->user_id === $user->id) {
            return true;
        }

        return $user->isStaff();
    }

    /**
     * @param  Builder<Upload>  $query
     * @return Builder<Upload>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isStaff()) {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($user): void {
            $inner->where('visibility', 'public')
                ->orWhere('user_id', $user->id);
        });
    }

    public function displayUrl(): string
    {
        if (is_string($this->path) && $this->path !== '') {
            return Storage::disk('public')->url($this->path);
        }

        return $this->media_url;
    }

    /**
     * Absolute URL Discord can fetch and unfurl as a GIF or picture.
     */
    public function publicMediaUrl(): string
    {
        if (is_string($this->path) && $this->path !== '') {
            return url(Storage::disk('public')->url($this->path));
        }

        return $this->media_url;
    }
}
