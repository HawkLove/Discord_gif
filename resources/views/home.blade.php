@extends('layouts.app')

@section('title', 'Loopix — GIFs for Discord')

@section('content')
    <main class="hero">
        <section class="hero-card">
            <p class="kicker">GIFs and pictures</p>
            <h1>Copy GIFs into Discord.</h1>
            <p class="lede">Paste a Giphy link or upload a file, tag it, keep it private or share it. Copy the link and paste it in a chat. Sign in with Discord — we’ll remember you next time.</p>
            <a class="btn discord-btn" href="{{ route('auth.discord') }}">Continue with Discord</a>
            <p class="hint">No password. Your Discord identity is stored so returning visits just work.</p>
        </section>
    </main>
@endsection
