<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Users\UserResource;
use App\Support\VoyagerSalesDashboard;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;

class TopCustomersTableWidget extends TableWidget
{
    protected static ?int $sort = -38;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Top customers'))
            ->description(__('By lifetime spend (all orders).'))
            ->emptyStateHeading(__('No customer spend data'))
            ->emptyStateDescription(__('Orders need a linked customer to appear here.'))
            ->records(fn (): Collection => VoyagerSalesDashboard::topCustomers()->map(fn (object $row): array => [
                'id' => (int) $row->id,
                'name' => (string) $row->name,
                'total_orders' => $row->total_orders,
            ]))
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label(__('Customer'))
                    ->weight('medium'),
                TextColumn::make('total_orders')
                    ->label(__('Total spend'))
                    ->money('NOK')
                    ->alignEnd(),
            ])
            ->contentFooter(view('filament.widgets.table-link-footer', [
                'url' => UserResource::getUrl(),
                'label' => __('View all users'),
            ]));
    }
}
