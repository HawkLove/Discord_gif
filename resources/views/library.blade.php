@extends('layouts.app')

@section('title', 'Library — Loopix')

@php
    $selectedTagId = (string) old('tag_id', session('selected_tag_id'));
@endphp

@section('content')
    <main class="page grid-2">
        <section class="panel" id="upload">
            <h2>Add a GIF or picture</h2>
            <form method="POST" action="{{ route('uploads.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="field">
                    <label for="giphy_url">Giphy link</label>
                    <input id="giphy_url" type="url" name="giphy_url" value="{{ old('giphy_url') }}" placeholder="https://giphy.com/gifs/...">
                </div>
                <div class="field">
                    <label for="file">Or upload a GIF / picture</label>
                    <input id="file" type="file" name="file" accept="image/gif,image/jpeg,image/png,image/webp">
                </div>
                <div class="field">
                    <label for="tag_id">Tag</label>
                    <select id="tag_id" name="tag_id" required>
                        <option value="" disabled @selected($selectedTagId === '')>Choose a tag</option>
                        @if ($globalTags->isNotEmpty())
                            <optgroup label="Shared">
                                @foreach ($globalTags as $tag)
                                    <option value="{{ $tag->id }}" @selected($selectedTagId === (string) $tag->id)>{{ $tag->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if ($personalTags->isNotEmpty())
                            <optgroup label="Personal">
                                @foreach ($personalTags as $tag)
                                    <option value="{{ $tag->id }}" @selected($selectedTagId === (string) $tag->id)>{{ $tag->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>
                <div class="field">
                    <label>Visibility</label>
                    <div class="seg">
                        <label>
                            <input type="radio" name="visibility" value="public" @checked(old('visibility', 'public') === 'public')>
                            Public
                        </label>
                        <label>
                            <input type="radio" name="visibility" value="private" @checked(old('visibility') === 'private')>
                            Private
                        </label>
                    </div>
                </div>
                <button class="btn" type="submit">Save</button>
            </form>

            <div class="personal-tags">
                <h3>Your personal tags</h3>
                <form method="POST" action="{{ route('personal-tags.store') }}" class="inline-form">
                    @csrf
                    <input type="text" name="name" required maxlength="50" placeholder="weekend">
                    <button class="btn btn-small" type="submit">Add tag</button>
                </form>
                @if ($personalTags->isNotEmpty())
                    <ul class="tag-list">
                        @foreach ($personalTags as $tag)
                            <li>
                                <span class="tag is-personal">{{ $tag->name }}</span>
                                @if ($tag->visible_uploads_count === 0)
                                    <form method="POST" action="{{ route('personal-tags.destroy', $tag) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-ghost btn-small btn-danger" type="submit">Remove</button>
                                    </form>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>

        <section class="library-head">
            <nav class="filters" aria-label="Filter by tag">
                <a class="chip @if ($currentTag === null) is-active @endif" href="{{ route('library') }}">All</a>
                @foreach ($availableTags as $tag)
                    <a
                        class="chip @if ($currentTag?->id === $tag->id) is-active @endif @if ($tag->isPersonal()) is-personal @endif"
                        href="{{ route('library', ['tag' => $tag->slug]) }}"
                    >
                        {{ $tag->name }}
                        <span class="chip-count">{{ $tag->visible_uploads_count }}</span>
                    </a>
                @endforeach
            </nav>

            @if ($uploads->isEmpty())
                <div class="panel empty">
                    @if ($currentTag)
                        Nothing with the {{ $currentTag->name }} tag yet.
                    @else
                        Nothing here yet. Add a Giphy link or a file to get started.
                    @endif
                </div>
            @else
                <div class="library">
                    @foreach ($uploads as $upload)
                        <article class="card">
                            <div class="card-media">
                                <img src="{{ $upload->displayUrl() }}" alt="{{ $upload->tag->name }}">
                            </div>
                            <div class="card-body">
                                <div class="meta">
                                    @if ($upload->tag->isAvailableTo(auth()->user()))
                                        <a class="tag @if ($upload->tag->isPersonal()) is-personal @endif" href="{{ route('library', ['tag' => $upload->tag->slug]) }}">{{ $upload->tag->name }}</a>
                                    @else
                                        <span class="tag">{{ $upload->tag->name }}</span>
                                    @endif
                                    <span class="vis">{{ $upload->visibility }}</span>
                                </div>
                                <button
                                    class="btn btn-small"
                                    type="button"
                                    data-copy-url="{{ $upload->publicMediaUrl() }}"
                                >Copy link</button>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
@endsection
