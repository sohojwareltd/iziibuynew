<?php

namespace App\Support\LegacyVoyager;

use Illuminate\Support\Facades\Storage;

/**
 * Drop-in helpers for code that previously used TCG\Voyager\Facades\Voyager.
 */
final class VoyagerFacade
{
    public static function image(?string $file, ?string $default = ''): string
    {
        if ($file === null || $file === '') {
            return (string) $default;
        }

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            return $file;
        }

        $disk = config('iziibuy.storage_disk', config('filesystems.default', 'public'));

        return (string) Storage::disk($disk)->url($file);
    }
}
