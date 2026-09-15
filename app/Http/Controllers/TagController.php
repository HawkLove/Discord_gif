<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGlobalTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        return view('tags.index', [
            'tags' => Tag::query()
                ->whereNull('user_id')
                ->withCount('uploads')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreGlobalTagRequest $request): RedirectResponse
    {
        $name = $request->validated('name');

        Tag::query()->create([
            'name' => $name,
            'slug' => Tag::slugFrom($name),
            'user_id' => null,
        ]);

        return redirect()
            ->route('tags.index')
            ->with('status', 'Tag added.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        abort_unless($tag->isGlobal(), 404);

        if ($tag->uploads()->exists()) {
            return back()->withErrors(['tag' => 'This tag is still used by uploads.']);
        }

        $tag->delete();

        return redirect()
            ->route('tags.index')
            ->with('status', 'Tag removed.');
    }
}
