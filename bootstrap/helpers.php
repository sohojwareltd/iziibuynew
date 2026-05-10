<?php

declare(strict_types=1);

if (! function_exists('setting')) {
    /**
     * Replaces the Voyager global `setting()` helper using config('settings.*').
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return config("settings.{$key}", $default);
    }
}
