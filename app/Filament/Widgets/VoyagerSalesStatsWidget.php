<?php

namespace App\Filament\Widgets;

use App\Support\VoyagerSalesDashboard;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VoyagerSalesStatsWidget extends StatsOverviewWidget
{
    /**
     * Below sales tables (-40 .. -38); above retailer overview (-19).
     */
    protected static ?int $sort = -37;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.voyager-sales-stats-overview';

    protected ?string $heading = null;

    protected ?string $description = null;

    /** Full-width row under dashboard tables for readable KPI tiles. */
    protected int|string|array $columnSpan = 'full';

    public function getSectionContentComponent(): Component
    {
        return Section::make()
            ->schema($this->getCachedStats())
            ->columns([
                'default' => 1,
                'sm' => 2,
                'xl' => 4,
            ])
            ->contained(false)
            ->gridContainer();
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Total Sales'), VoyagerSalesDashboard::moneyFormat(VoyagerSalesDashboard::totalSalesSum()))
                ->description(__('Today').': +'.VoyagerSalesDashboard::moneyFormat(VoyagerSalesDashboard::todaySalesSum()))
                ->extraAttributes([
                    'class' => 'fi-voyager-sales-stat-tile',
                ]),
            Stat::make(__('Total Shop'), number_format(VoyagerSalesDashboard::shopsCount()))
                ->description(__('So far today').' +'.VoyagerSalesDashboard::shopsTodayCount())
                ->extraAttributes([
                    'class' => 'fi-voyager-sales-stat-tile',
                ]),
            Stat::make(__('Total Customers'), number_format(VoyagerSalesDashboard::totalCustomersCount()))
                ->description(__('So far today').' +'.VoyagerSalesDashboard::customersRegisteredTodayCount())
                ->extraAttributes([
                    'class' => 'fi-voyager-sales-stat-tile',
                ]),
            Stat::make(__('Average Order'), VoyagerSalesDashboard::moneyFormat(VoyagerSalesDashboard::averageOrderValue()))
                ->extraAttributes([
                    'class' => 'fi-voyager-sales-stat-tile',
                ]),
        ];
    }
}
