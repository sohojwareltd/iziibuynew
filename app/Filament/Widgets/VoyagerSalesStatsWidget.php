<?php

namespace App\Filament\Widgets;

use App\Support\VoyagerSalesDashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VoyagerSalesStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -45;

    protected ?string $heading = 'Sales dashboard';

    protected ?string $description = 'Totals match the legacy Voyager admin home (paid orders, shops, customers).';

    /**
     * Responsive grid: readable tiles on mobile, 2×2 then 1×4 on wide screens.
     *
     * @var int|array<string, int|null>|null
     */
    protected int|array|null $columns = [
        'default' => 1,
        'sm' => 2,
        '2xl' => 4,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make(__('Total Sales'), VoyagerSalesDashboard::moneyFormat(VoyagerSalesDashboard::totalSalesSum()))
                ->description(__('Today').': +'.VoyagerSalesDashboard::moneyFormat(VoyagerSalesDashboard::todaySalesSum()))
                ->descriptionIcon(Heroicon::OutlinedArrowTrendingUp)
                ->color('primary')
                ->icon(Heroicon::OutlinedBanknotes),
            Stat::make(__('Total Shop'), number_format(VoyagerSalesDashboard::shopsCount()))
                ->description(__('So far today').' +'.VoyagerSalesDashboard::shopsTodayCount())
                ->descriptionIcon(Heroicon::OutlinedBuildingStorefront)
                ->color('success')
                ->icon(Heroicon::OutlinedBuildingStorefront),
            Stat::make(__('Total Customers'), number_format(VoyagerSalesDashboard::totalCustomersCount()))
                ->description(__('So far today').' +'.VoyagerSalesDashboard::customersRegisteredTodayCount())
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('info')
                ->icon(Heroicon::OutlinedUsers),
            Stat::make(__('Average Order'), VoyagerSalesDashboard::moneyFormat(VoyagerSalesDashboard::averageOrderValue()))
                ->description(__('Mean ticket on paid orders'))
                ->descriptionIcon(Heroicon::OutlinedCalculator)
                ->color('warning')
                ->icon(Heroicon::OutlinedCalculator),
        ];
    }
}
