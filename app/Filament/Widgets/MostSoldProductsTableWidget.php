<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Support\VoyagerSalesDashboard;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Str;

class MostSoldProductsTableWidget extends TableWidget
{
    protected static ?int $sort = -39;

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
            ->heading(__('Top products'))
            ->emptyStateHeading(__('No products'))
            ->emptyStateDescription(__('Broaden your filters or add catalog items.'))
            ->query(
                VoyagerSalesDashboard::mostSoldProductsForTable()->limit(5)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->formatStateUsing(fn (?string $state): string => Str::limit((string) $state, 20, ''))
                    ->url(fn (Product $record): string => ProductResource::getUrl())
                    ->color('warning'),
                TextColumn::make('price')
                    ->label(__('Price'))
                    ->formatStateUsing(fn ($state): string => is_numeric($state) ? (string) $state : '—')
                    ->alignEnd(),
                TextColumn::make('sale_count')
                    ->label(__('Qty'))
                    ->numeric()
                    ->alignEnd(),
            ])
            ->contentFooter(view('filament.widgets.table-link-footer', [
                'url' => ProductResource::getUrl(),
                'label' => __('View all products'),
            ]));
    }
}
