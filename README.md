# Loopix

Collect GIFs and pictures, then copy the link into Discord.

Sign in with Discord (your identity is stored so the next visit reuses the same account). Add a Giphy link or upload a file, pick a tag, choose public or private, then copy the media link and paste it in a Discord chat. Discord loads that URL as a GIF or picture.

## Discord OAuth2 redirect

In the [Discord Developer Portal](https://discord.com/developers/applications) open your app → **OAuth2** → **Redirects** and add:

```
http://localhost:8000/auth/discord/callback
```

That must match `.env`:

```
DISCORD_REDIRECT_URI=http://localhost:8000/auth/discord/callback
```

If you later host Loopix on a real domain, add that URL too, for example `https://loopix.example.com/auth/discord/callback`, and set `APP_URL` / `DISCORD_REDIRECT_URI` to match.

Also copy the **Client ID** and **Client Secret** into `.env` as `DISCORD_CLIENT_ID` and `DISCORD_CLIENT_SECRET`.

## Roles

Built-in roles: `user`, `admin`, and `owner`. Admins and owners can add more named roles and assign them.

New Discord logins start as `user`. The first account on a fresh database also gets `owner` so you can add roles and assign them.

## Setup

1. PHP 8.3+ and Composer.
2. Copy `.env.example` to `.env` and run `composer install`, `php artisan key:generate`.
3. SQLite is the default. Run `php artisan migrate`.
4. Set Discord OAuth values as above.
5. `php artisan storage:link` so uploaded files are public.
6. `php artisan serve`.

## Tests

```bash
php artisan test
```
