<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filament\Resources\CmsMenuItems\Pages\MenuBuilder;
use App\Services\Cms\AdminPanelNavigationBuilder;
use Filament\Events\ServingFilament;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CmsNavigationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(ServingFilament::class, function (): void {
            Filament::registerNavigationItems([
                NavigationItem::make(__('Menu builder'))
                    ->url(MenuBuilder::getUrl())
                    ->icon(Heroicon::OutlinedSquares2x2)
                    ->group('site')
                    ->sort(18)
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.cms-menu-items.*')),
            ]);

            app(AdminPanelNavigationBuilder::class)->registerSupplementaryItems();
        });
    }
}
