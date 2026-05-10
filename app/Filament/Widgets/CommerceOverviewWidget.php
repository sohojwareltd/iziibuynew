<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CommerceOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -20;

    protected ?string $heading = 'Commerce';

    protected ?string $description = 'Core counts across your marketplace.';

    protected function getStats(): array
    {
        return [
            Stat::make(__('Orders'), number_format(Order::query()->count()))
                ->description(__('All-time orders'))
                ->icon(Heroicon::OutlinedShoppingCart),
            Stat::make(__('Shops'), number_format(Shop::query()->count()))
                ->description(__('Active storefronts'))
                ->icon(Heroicon::OutlinedBuildingStorefront),
            Stat::make(__('Products'), number_format(Product::query()->count()))
                ->description(__('Catalog items'))
                ->icon(Heroicon::OutlinedCube),
            Stat::make(__('Users'), number_format(User::query()->count()))
                ->description(__('Registered accounts'))
                ->icon(Heroicon::OutlinedUsers),
        ];
    }
}
