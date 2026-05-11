<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Read key/value rows from {@see SiteSetting} (replaces common Voyager Setting lookups).
 */
final class SiteSettings
{
    /**
     * @param  non-empty-string  $key
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return SiteSetting::query()->where('key', $key)->value('value') ?? $default;
    }
}
