<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'user_id'])]
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Tag $tag): void {
            $tag->unique_key = $tag->scopeKey();
        });
    }

    public static function slugFrom(string $name): string
    {
        $slug = Str::slug($name);

        return $slug !== '' ? $slug : Str::lower($name);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Upload, $this>
     */
    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class);
    }

    public function isGlobal(): bool
    {
        return $this->user_id === null;
    }

    public function isPersonal(): bool
    {
        return $this->user_id !== null;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isAvailableTo(User $user): bool
    {
        return $this->isGlobal() || $this->isOwnedBy($user);
    }

    public function scopeKey(): string
    {
        return $this->user_id === null
            ? 'global:'.$this->slug
            : 'user:'.$this->user_id.':'.$this->slug;
    }

    /**
     * @param  Builder<Tag>  $query
     * @return Builder<Tag>
     */
    public function scopeAvailableTo(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $inner) use ($user): void {
            $inner->whereNull('user_id')
                ->orWhere('user_id', $user->id);
        });
    }
}
