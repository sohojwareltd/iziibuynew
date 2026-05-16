<?php

declare(strict_types=1);

namespace App\Enums;

enum MenuContext: string
{
    case Frontend = 'frontend';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Frontend => __('Frontend'),
            self::Admin => __('Admin panel'),
        };
    }
}
