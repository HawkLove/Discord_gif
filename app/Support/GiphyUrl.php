<?php

namespace App\Support;

class GiphyUrl
{
    /**
     * Common Giphy share and media hosts.
     */
    public static function isValid(string $url): bool
    {
        $parts = parse_url($url);

        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        if (! in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($parts['host']);

        return $host === 'giphy.com'
            || $host === 'www.giphy.com'
            || $host === 'i.giphy.com'
            || preg_match('/^media\d*\.giphy\.com$/', $host) === 1;
    }

    /**
     * Resolve a share URL to a direct GIF/media URL Discord can display.
     */
    public static function mediaUrl(string $url): string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        if ($host === 'i.giphy.com' || preg_match('/^media\d*\.giphy\.com$/', $host) === 1) {
            return $url;
        }

        if (preg_match('#/gifs/([^/?]+)#', $path, $matches) === 1) {
            return 'https://media.giphy.com/media/'.self::gifIdFromSlug($matches[1]).'/giphy.gif';
        }

        if (preg_match('#/media/([^/]+)#', $path, $matches) === 1) {
            return 'https://media.giphy.com/media/'.$matches[1].'/giphy.gif';
        }

        return $url;
    }

    protected static function gifIdFromSlug(string $slug): string
    {
        if (str_contains($slug, '-')) {
            return (string) substr($slug, (int) strrpos($slug, '-') + 1);
        }

        return $slug;
    }
}
