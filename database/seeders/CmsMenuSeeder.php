<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MenuContext;
use App\Enums\MenuLinkType;
use App\Models\CmsMenu;
use App\Models\CmsMenuItem;
use App\Services\Cms\LegacyAdminMenuSynchronizer;
use Illuminate\Database\Seeder;

class CmsMenuSeeder extends Seeder
{
    public function run(): void
    {
        $header = CmsMenu::query()->updateOrCreate(
            ['slug' => 'main-header'],
            [
                'name' => 'Main header',
                'context' => MenuContext::Frontend,
                'location' => 'header',
                'is_active' => true,
                'replaces_panel_navigation' => false,
            ],
        );

        $defaults = [
            ['title' => __('words.home'), 'route_name' => 'home', 'sort_order' => 0],
            ['title' => __('words.about'), 'route_name' => 'about', 'sort_order' => 10],
            ['title' => __('words.contact'), 'route_name' => 'contact', 'sort_order' => 20],
        ];

        foreach ($defaults as $item) {
            CmsMenuItem::query()->updateOrCreate(
                [
                    'cms_menu_id' => $header->id,
                    'route_name' => $item['route_name'],
                    'parent_id' => null,
                ],
                [
                    'title' => $item['title'],
                    'link_type' => MenuLinkType::Route,
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ],
            );
        }

        app(LegacyAdminMenuSynchronizer::class)->sync();
    }
}
