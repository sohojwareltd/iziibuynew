<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_menus', function (Blueprint $table): void {
            $table->string('context', 32)->default('frontend')->after('slug');
            $table->boolean('is_active')->default(true)->after('location');
            $table->boolean('replaces_panel_navigation')->default(false)->after('is_active');
        });

        Schema::table('cms_menu_items', function (Blueprint $table): void {
            $table->string('link_type', 32)->default('url')->after('title');
            $table->string('resource_class')->nullable()->after('route_name');
            $table->string('navigation_group')->nullable()->after('icon');
            $table->boolean('is_active')->default(true)->after('open_new_tab');
        });
    }

    public function down(): void
    {
        Schema::table('cms_menu_items', function (Blueprint $table): void {
            $table->dropColumn([
                'link_type',
                'resource_class',
                'navigation_group',
                'is_active',
            ]);
        });

        Schema::table('cms_menus', function (Blueprint $table): void {
            $table->dropColumn([
                'context',
                'is_active',
                'replaces_panel_navigation',
            ]);
        });
    }
};
