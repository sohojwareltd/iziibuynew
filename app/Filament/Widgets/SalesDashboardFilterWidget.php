<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SalesDashboardFilterWidget extends Widget
{
    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.sales-dashboard-filters';

    protected static ?int $sort = -50;

    protected int|string|array $columnSpan = 'full';
}
