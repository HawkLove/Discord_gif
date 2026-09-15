<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Loopix')</title>
    <link rel="icon" href="/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="{{ auth()->check() ? route('library') : route('login') }}">
                <img class="mark" src="/logo.png" alt="Loopix">
                Loopix
            </a>
            <nav class="nav">
                @auth
                    <a href="{{ route('library') }}">Library</a>
                    @if (auth()->user()->isStaff())
                        <a href="{{ route('tags.index') }}">Tags</a>
                        <a href="{{ route('roles.index') }}">Roles</a>
                    @endif
                    <span class="who">
                        @if (auth()->user()->avatar)
                            <img class="avatar" src="{{ auth()->user()->avatar }}" alt="">
                        @endif
                        {{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-ghost btn-small" type="submit">Log out</button>
                    </form>
                @else
                    <a class="btn btn-small" href="{{ route('auth.discord') }}">Continue with Discord</a>
                @endauth
            </nav>
        </header>

        @if (session('status'))
            <p class="flash">{{ session('status') }}</p>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>
    <script src="/js/app.js" defer></script>
</body>
</html>
