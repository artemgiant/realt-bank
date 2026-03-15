<?php

if (!function_exists('versioned_asset')) {
    /**
     * Генерирует URL ассета с версионным параметром для сброса кеша браузера.
     */
    function versioned_asset(string $path): string
    {
        $version = config('app.asset_version', substr(md5(time()), 0, 5));
        return asset($path) . '?v=' . $version;
    }
}
