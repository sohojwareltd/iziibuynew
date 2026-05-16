<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Support\VoyagerSalesDashboard;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Carbon;

class LatestOrdersTableWidget extends TableWidget
{
    protected static ?int $sort = -40;

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.voyager-table-widget';

    /**
     * @var int|string|array<string, int|string|null>
     */
    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 6,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Recent orders'))
            ->emptyStateHeading(__('No orders yet'))
            ->emptyStateDescription(__('Adjust filters or wait for new paid orders.'))
            ->query(
                VoyagerSalesDashboard::paidOrdersForLatestTable()
                    ->with(['user'])
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('id')
                    ->label(__('ID'))
                    ->weight('medium')
                    ->url(fn (Order $record): string => OrderResource::getUrl())
                    ->color('warning'),
                TextColumn::make('total')
                    ->label(__('Price'))
                    ->formatStateUsing(fn ($state): string => is_numeric($state) ? (string) $state : '—')
                    ->alignEnd(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->formatStateUsing(fn ($state): string => $state instanceof Carbon ? $state->format('d-m-Y') : '—'),
                TextColumn::make('user.name')
                    ->label(__('User name'))
                    ->placeholder('—'),
            ])
            ->contentFooter(view('filament.widgets.table-link-footer', [
                'url' => OrderResource::getUrl(),
                'label' => __('View all orders'),
            ]));
    }
}
