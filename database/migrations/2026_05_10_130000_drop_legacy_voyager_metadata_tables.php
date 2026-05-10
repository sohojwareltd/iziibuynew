<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Safe no-op if tables were never created. Run after data is fully on Filament.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'data_rows',
            'data_types',
            'menu_items',
            'menus',
            'translations',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        // Irreversible: Voyager schema not restored.
    }
};
