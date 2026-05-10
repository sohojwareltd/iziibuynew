<?php

namespace App\Filament\Resources\Shops;

use App\Filament\Resources\Shops\Pages\ManageShops;
use App\Models\Shop;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
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

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static string|\UnitEnum|null $navigationGroup = 'commerce';

    protected static ?int $navigationSort = 20;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('retailer_id')
                    ->numeric(),
                TextInput::make('user_name')
                    ->required(),
                Textarea::make('terms')
                    ->columnSpanFull(),
                TextInput::make('payment_order_id'),
                TextInput::make('tax')
                    ->numeric(),
                Toggle::make('status'),
                TextInput::make('subscription_id')
                    ->required(),
                Textarea::make('payment_url')
                    ->columnSpanFull(),
                Toggle::make('establishment'),
                TextInput::make('establishment_cost')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('monthly_cost')
                    ->numeric()
                    ->prefix('$'),
                Toggle::make('service_establishment'),
                TextInput::make('service_establishment_cost')
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('service_monthly_fee')
                    ->numeric()
                    ->default(0),
                Toggle::make('can_provide_service'),
                TextInput::make('per_user_fee')
                    ->numeric(),
                TextInput::make('locations'),
                TextInput::make('selling_location_mode')
                    ->numeric(),
                Toggle::make('contract_signed'),
                Toggle::make('contract_status'),
                TextInput::make('default_currency')
                    ->default('NOK'),
                Textarea::make('currencies')
                    ->columnSpanFull(),
                Textarea::make('country')
                    ->columnSpanFull(),
                Textarea::make('default_language')
                    ->columnSpanFull(),
                Textarea::make('contract_url')
                    ->columnSpanFull(),
                TextInput::make('area_id')
                    ->numeric(),
                Toggle::make('store_as_pickup_point'),
                DateTimePicker::make('paid_at'),
                TextInput::make('area'),
                Toggle::make('is_demo')
                    ->required(),
                TextInput::make('previous_retailer')
                    ->numeric(),
                DatePicker::make('retailer_joined_at'),
                DatePicker::make('previous_retailer_suspended_at'),
                TextInput::make('shopperId'),
                TextInput::make('subscriptionMethod')
                    ->required()
                    ->default('quickpay'),
                TextInput::make('paymentMethod')
                    ->required()
                    ->default('quickpay'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('retailer_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('user_name'),
                TextEntry::make('terms')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('payment_order_id')
                    ->placeholder('-'),
                TextEntry::make('tax')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('status')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('subscription_id'),
                TextEntry::make('payment_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('establishment')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('establishment_cost')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('monthly_cost')
                    ->money()
                    ->placeholder('-'),
                IconEntry::make('service_establishment')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('service_establishment_cost')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('service_monthly_fee')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('can_provide_service')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('per_user_fee')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('locations')
                    ->placeholder('-'),
                TextEntry::make('selling_location_mode')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('contract_signed')
                    ->boolean()
                    ->placeholder('-'),
                IconEntry::make('contract_status')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('default_currency')
                    ->placeholder('-'),
                TextEntry::make('currencies')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('country')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('default_language')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('contract_url')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('area_id')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('store_as_pickup_point')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('area')
                    ->placeholder('-'),
                IconEntry::make('is_demo')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('previous_retailer')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('retailer_joined_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('previous_retailer_suspended_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('shopperId')
                    ->placeholder('-'),
                TextEntry::make('subscriptionMethod'),
                TextEntry::make('paymentMethod'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('retailer_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user_name')
                    ->searchable(),
                TextColumn::make('payment_order_id')
                    ->searchable(),
                TextColumn::make('tax')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('subscription_id')
                    ->searchable(),
                IconColumn::make('establishment')
                    ->boolean(),
                TextColumn::make('establishment_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('monthly_cost')
                    ->money()
                    ->sortable(),
                IconColumn::make('service_establishment')
                    ->boolean(),
                TextColumn::make('service_establishment_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('service_monthly_fee')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('can_provide_service')
                    ->boolean(),
                TextColumn::make('per_user_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('locations')
                    ->searchable(),
                TextColumn::make('selling_location_mode')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('contract_signed')
                    ->boolean(),
                IconColumn::make('contract_status')
                    ->boolean(),
                TextColumn::make('default_currency')
                    ->searchable(),
                TextColumn::make('area_id')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('store_as_pickup_point')
                    ->boolean(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('area')
                    ->searchable(),
                IconColumn::make('is_demo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('previous_retailer')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('retailer_joined_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('previous_retailer_suspended_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('shopperId')
                    ->searchable(),
                TextColumn::make('subscriptionMethod')
                    ->searchable(),
                TextColumn::make('paymentMethod')
                    ->searchable(),
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
            'index' => ManageShops::route('/'),
        ];
    }
}
