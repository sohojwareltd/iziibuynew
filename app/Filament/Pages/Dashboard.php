<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminQuickLinksWidget;
use App\Filament\Widgets\LatestOrdersTableWidget;
use App\Filament\Widgets\MostSoldProductsTableWidget;
use App\Filament\Widgets\RetailerFinanceOverviewWidget;
use App\Filament\Widgets\SalesDashboardFilterWidget;
use App\Filament\Widgets\TopCustomersTableWidget;
use App\Filament\Widgets\VoyagerSalesStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class Dashboard extends BaseDashboard
{
    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...(method_exists($this, 'getFiltersForm') ? [$this->getFiltersFormContentComponent()] : []),

                Section::make(__('Sales dashboard'))
                    ->description(__('Filters, activity tables, and headline KPIs.'))
                    ->icon(Heroicon::OutlinedPresentationChartBar)
                    ->schema([
                        Grid::make(1)
                            ->schema($this->getWidgetsSchemaComponents([
                                SalesDashboardFilterWidget::class,
                            ])),
                        Grid::make(2)
                            ->schema($this->getWidgetsSchemaComponents([
                                LatestOrdersTableWidget::class,
                                MostSoldProductsTableWidget::class,
                            ])),
                        Grid::make(1)
                            ->schema($this->getWidgetsSchemaComponents([
                                TopCustomersTableWidget::class,
                            ])),
                        Grid::make(1)
                            ->schema($this->getWidgetsSchemaComponents([
                                VoyagerSalesStatsWidget::class,
                            ])),
                    ])
                    ->columns(1)
                    ->compact(),

                Section::make(__('Retailers & payouts'))
                    ->description(__('Partner network and withdrawal queue.'))
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->schema([
                        Grid::make(1)
                            ->schema($this->getWidgetsSchemaComponents([
                                RetailerFinanceOverviewWidget::class,
                            ])),
                    ])
                    ->columns(1)
                    ->compact(),

                Section::make(__('Shortcuts'))
                    ->description(__('Jump to common admin areas.'))
                    ->icon(Heroicon::OutlinedSquares2x2)
                    ->schema([
                        Grid::make(1)
                            ->schema($this->getWidgetsSchemaComponents([
                                AdminQuickLinksWidget::class,
                            ])),
                    ])
                    ->columns(1)
                    ->compact(),
            ]);
    }

    /**
     * Widget layout is defined explicitly in {@see content()} — hide the default discovery grid.
     *
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [];
    }
}
