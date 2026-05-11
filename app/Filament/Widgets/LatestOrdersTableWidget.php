<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Support\VoyagerSalesDashboard;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrdersTableWidget extends TableWidget
{
    protected static ?int $sort = -40;

    /**
     * @var int|string|array<string, int|string|null>
     */
    protected int|string|array $columnSpan = [
        'lg' => 6,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Latest Orders'))
            ->description(__('Last five paid orders for the current filters.'))
            ->emptyStateHeading(__('No orders yet'))
            ->emptyStateDescription(__('Adjust filters or wait for new paid orders.'))
            ->query(
                VoyagerSalesDashboard::paidOrdersForLatestTable()->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->weight('medium'),
                TextColumn::make('total')
                    ->label(__('Total'))
                    ->money('NOK')
                    ->alignEnd(),
                TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime('M j, Y'),
            ])
            ->contentFooter(view('filament.widgets.table-link-footer', [
                'url' => OrderResource::getUrl(),
                'label' => __('View all orders'),
            ]));
    }
}
