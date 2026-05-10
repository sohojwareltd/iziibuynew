<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\ManageOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\UnitEnum|null $navigationGroup = 'commerce';

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('store_id')
                    ->numeric(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('shop_id')
                    ->required()
                    ->numeric(),
                TextInput::make('referral_code')
                    ->numeric(),
                TextInput::make('payment_id'),
                Textarea::make('payment_url')
                    ->columnSpanFull(),
                TextInput::make('discount')
                    ->numeric(),
                TextInput::make('discount_code'),
                TextInput::make('subtotal')
                    ->numeric(),
                TextInput::make('tax')
                    ->numeric(),
                TextInput::make('shipping_cost')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('shipping_method'),
                TextInput::make('total')
                    ->numeric(),
                TextInput::make('payment_method'),
                Toggle::make('status')
                    ->required(),
                TextInput::make('refund')
                    ->numeric(),
                TextInput::make('payment_status')
                    ->numeric(),
                TextInput::make('is_company')
                    ->numeric()
                    ->default(0),
                TextInput::make('currency')
                    ->required()
                    ->default('NOK'),
                TextInput::make('type')
                    ->numeric()
                    ->default(0),
                DatePicker::make('paid_at'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('store_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('shop_id')
                    ->numeric(),
                TextEntry::make('referral_code')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('payment_id')
                    ->placeholder('-'),
                TextEntry::make('payment_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('discount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('discount_code')
                    ->placeholder('-'),
                TextEntry::make('subtotal')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tax')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('shipping_cost')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('shipping_method')
                    ->placeholder('-'),
                TextEntry::make('total')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                IconEntry::make('status')
                    ->boolean(),
                TextEntry::make('refund')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('payment_status')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('is_company')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('currency'),
                TextEntry::make('type')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->date()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('store_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shop_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('referral_code')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_id')
                    ->searchable(),
                TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_code')
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('shipping_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('shipping_method')
                    ->searchable(),
                TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->searchable(),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('refund')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_company')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('type')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paid_at')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageOrders::route('/'),
        ];
    }
}
