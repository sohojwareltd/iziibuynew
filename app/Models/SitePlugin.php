<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePlugin extends Model
{
    protected $guarded = [];

    protected $table = 'site_plugins';

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'config' => 'array',
        ];
    }
}
