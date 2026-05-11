<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Support\VoyagerSalesDashboard;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Str;

class MostSoldProductsTableWidget extends TableWidget
{
    protected static ?int $sort = -39;

    /**
     * @var int|string|array<string, int|string|null>
     */
    protected int|string|array $columnSpan = [
        'lg' => 6,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Most Sold Products'))
            ->description(__('By sale count for the current filters.'))
            ->emptyStateHeading(__('No products'))
            ->emptyStateDescription(__('Broaden your filters or add catalog items.'))
            ->query(
                VoyagerSalesDashboard::mostSoldProductsForTable()->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label(__('Product'))
                    ->formatStateUsing(fn (?string $state): string => Str::limit((string) $state, 28, '…')),
                TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('NOK')
                    ->alignEnd(),
                TextColumn::make('sku')
                    ->label(__('SKU'))
                    ->badge()
                    ->color('gray'),
            ])
            ->contentFooter(view('filament.widgets.table-link-footer', [
                'url' => ProductResource::getUrl(),
                'label' => __('View all products'),
            ]));
    }
}
