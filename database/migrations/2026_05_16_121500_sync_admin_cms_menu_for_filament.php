<?php

declare(strict_types=1);

use App\Enums\MenuContext;
use App\Models\CmsMenu;
use App\Services\Cms\LegacyAdminMenuSynchronizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_menus')) {
            return;
        }

        app(LegacyAdminMenuSynchronizer::class)->sync();
    }

    public function down(): void
    {
        if (! Schema::hasTable('cms_menus')) {
            return;
        }

        CmsMenu::query()
            ->where('slug', LegacyAdminMenuSynchronizer::ADMIN_MENU_SLUG)
            ->update([
                'context' => MenuContext::Frontend,
                'replaces_panel_navigation' => false,
            ]);
    }
};
