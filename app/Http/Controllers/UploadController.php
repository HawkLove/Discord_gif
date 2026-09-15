<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUploadRequest;
use App\Models\Tag;
use App\Models\Upload;
use App\Models\User;
use App\Services\UploadStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UploadController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $currentTag = $this->currentTag($request, $user);

        $availableTags = Tag::query()
            ->availableTo($user)
            ->withCount([
                'uploads as visible_uploads_count' => fn (Builder $query) => $query->visibleTo($user),
            ])
            ->orderByRaw('user_id is not null')
            ->orderBy('name')
            ->get();

        $uploads = Upload::query()
            ->with(['user', 'tag'])
            ->visibleTo($user)
            ->when($currentTag !== null, fn (Builder $query) => $query->where('tag_id', $currentTag->id))
            ->latest()
            ->get();

        return view('library', [
            'uploads' => $uploads,
            'currentTag' => $currentTag,
            'availableTags' => $availableTags,
            'globalTags' => $availableTags->filter(fn (Tag $tag): bool => $tag->isGlobal())->values(),
            'personalTags' => $availableTags->filter(fn (Tag $tag): bool => $tag->isPersonal())->values(),
        ]);
    }

    public function store(StoreUploadRequest $request, UploadStore $store): RedirectResponse
    {
        $store->store($request->user(), $request->validated());

        return redirect()
            ->route('library')
            ->with('status', 'Saved.');
    }

    protected function currentTag(Request $request, User $user): ?Tag
    {
        if (! $request->filled('tag')) {
            return null;
        }

        $tag = Tag::query()
            ->availableTo($user)
            ->where('slug', $request->string('tag')->toString())
            ->orderByRaw('user_id is null')
            ->first();

        abort_if($tag === null, 404);

        return $tag;
    }
}
