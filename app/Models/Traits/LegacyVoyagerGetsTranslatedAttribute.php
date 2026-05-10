<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * Voyager called {@see getTranslatedAttribute()} on several models; Spatie uses {@see HasTranslations::getTranslation()} instead.
 */
trait LegacyVoyagerGetsTranslatedAttribute
{
    public function getTranslatedAttribute(string $key, ?string $locale = null): mixed
    {
        $locale ??= app()->getLocale();

        if (in_array(HasTranslations::class, class_uses_recursive(static::class), true)) {
            /** @var array<int, string>|string $translatable */
            $translatable = property_exists($this, 'translatable') ? $this->translatable : [];
            if (is_string($translatable)) {
                $translatable = [$translatable];
            }
            if (in_array($key, $translatable, true)) {
                /** @var Model&HasTranslations $this */
                return $this->getTranslation($key, $locale);
            }
        }

        return $this->getAttribute($key);
    }
}
