<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Drop-in helpers for legacy Blade that called {@see \TCG\Voyager\Facades\Voyager}.
 */
final class Voyager
{
    public static function image(?string $file, string $default = ''): string
    {
        if ($file === null || $file === '') {
            return $default;
        }

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            return $file;
        }

        $diskName = config('iziibuy.storage_disk')
            ?? config('voyager.storage.disk')
            ?? config('filesystems.default', 'public');

        return Storage::disk($diskName)->url($file);
    }

    public static function setting(string $key, mixed $default = null): mixed
    {
        return setting($key, $default);
    }

    /**
     * @return class-string<Model>
     */
    public static function model(string $name): string
    {
        $class = 'App\\Models\\'.$name;

        if (! class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf('Voyager model [%s] resolves to missing class [%s].', $name, $class)
            );
        }

        return $class;
    }
}
