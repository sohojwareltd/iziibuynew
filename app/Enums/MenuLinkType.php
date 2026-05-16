<?php

declare(strict_types=1);

namespace App\Enums;

enum MenuLinkType: string
{
    case Url = 'url';
    case Route = 'route';
    case Resource = 'resource';

    public function label(): string
    {
        return match ($this) {
            self::Url => __('URL / path'),
            self::Route => __('Named route'),
            self::Resource => __('Filament resource'),
        };
    }
}
