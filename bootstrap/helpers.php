<?php

declare(strict_types=1);

if (! function_exists('setting')) {
    /**
     * Replaces the Voyager global `setting()` helper.
     * Checks config('settings.*') first, then falls back to the database.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return config("settings.{$key}")
            ?? \App\Support\SiteSettings::get($key)
            ?? $default;
    }
}
