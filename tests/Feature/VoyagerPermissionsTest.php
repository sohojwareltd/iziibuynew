<?php

declare(strict_types=1);

use App\Support\VoyagerPermissions;
use Illuminate\Support\Facades\Artisan;

test('voyager permission key matches Voyager BREAD slug pattern', function () {
    expect(VoyagerPermissions::permissionKey('browse', 'posts'))->toBe('browse_posts')
        ->and(VoyagerPermissions::permissionKey('READ', 'menu-items'))->toBe('read_menu_items');
});

test('import voyager permissions command is registered', function () {
    expect(Artisan::all())->toHaveKey('iziibuy:import-voyager-permissions');
});
