<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonalTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PersonalTagController extends Controller
{
    public function store(StorePersonalTagRequest $request): RedirectResponse
    {
        $name = $request->validated('name');

        $tag = Tag::query()->create([
            'name' => $name,
            'slug' => Tag::slugFrom($name),
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('library')
            ->with('status', 'Personal tag added.')
            ->with('selected_tag_id', $tag->id);
    }

    public function destroy(Request $request, Tag $tag): RedirectResponse
    {
        abort_unless($tag->isPersonal() && $tag->isOwnedBy($request->user()), 404);

        if ($tag->uploads()->exists()) {
            return back()->withErrors(['tag' => 'This tag is still used by uploads.']);
        }

        $tag->delete();

        return redirect()
            ->route('library')
            ->with('status', 'Personal tag removed.');
    }
}
