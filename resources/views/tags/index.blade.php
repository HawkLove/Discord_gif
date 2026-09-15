@extends('layouts.app')

@section('title', 'Tags — Loopix')

@section('content')
    <main class="page">
        <section class="panel">
            <h2>Add a shared tag</h2>
            <form method="POST" action="{{ route('tags.store') }}" class="inline-form">
                @csrf
                <input type="text" name="name" required maxlength="50" placeholder="reaction">
                <button class="btn btn-small" type="submit">Add tag</button>
            </form>
            <p class="hint">Shared tags are available to everyone when they save a GIF. Users can also add personal tags from the library.</p>
        </section>

        <section class="panel">
            <h2>Shared tags</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Uploads</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tags as $tag)
                        <tr>
                            <td>{{ $tag->name }}</td>
                            <td>{{ $tag->slug }}</td>
                            <td>{{ $tag->uploads_count }}</td>
                            <td>
                                @if ($tag->uploads_count === 0)
                                    <form method="POST" action="{{ route('tags.destroy', $tag) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-ghost btn-small btn-danger" type="submit">Remove</button>
                                    </form>
                                @else
                                    <span class="muted">In use</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No shared tags yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
@endsection
