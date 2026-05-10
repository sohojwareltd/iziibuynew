<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\ManageProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\UnitEnum|null $navigationGroup = 'commerce';

    protected static ?int $navigationSort = 30;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('shop_id')
                    ->numeric(),
                TextInput::make('parent_id')
                    ->numeric(),
                TextInput::make('item'),
                TextInput::make('name'),
                TextInput::make('ean'),
                TextInput::make('slug'),
                Textarea::make('areas')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('saleprice')
                    ->numeric(),
                TextInput::make('retailerprice')
                    ->numeric(),
                TextInput::make('retailersaleprice')
                    ->numeric(),
                Textarea::make('details')
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('quantity')
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image(),
                Textarea::make('images')
                    ->columnSpanFull(),
                TextInput::make('view')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('sale_count')
                    ->required()
                    ->numeric()
                    ->default(1),
                Toggle::make('status'),
                TextInput::make('tax')
                    ->numeric(),
                Toggle::make('is_variable'),
                TextInput::make('variation'),
                TextInput::make('length')
                    ->numeric(),
                TextInput::make('width')
                    ->numeric(),
                TextInput::make('height')
                    ->numeric(),
                TextInput::make('weight')
                    ->numeric(),
                Toggle::make('featured'),
                TextInput::make('discount')
                    ->numeric(),
                TextInput::make('qrcode'),
                TextInput::make('order_no')
                    ->numeric(),
                Toggle::make('pin')
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('shop_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('parent_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('item')
                    ->placeholder('-'),
                TextEntry::make('name')
                    ->placeholder('-'),
                TextEntry::make('ean')
                    ->placeholder('-'),
                TextEntry::make('slug')
                    ->placeholder('-'),
                TextEntry::make('areas')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('saleprice')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('retailerprice')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('retailersaleprice')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('details')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('sku')
                    ->label('SKU')
                    ->placeholder('-'),
                TextEntry::make('quantity')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('images')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('view')
                    ->numeric(),
                TextEntry::make('sale_count')
                    ->numeric(),
                IconEntry::make('status')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('tax')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_variable')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('variation')
                    ->placeholder('-'),
                TextEntry::make('length')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('width')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('height')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('weight')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('featured')
                    ->boolean()
                    ->placeholder('-'),
                TextEntry::make('discount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('qrcode')
                    ->placeholder('-'),
                TextEntry::make('order_no')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('pin')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shop_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('parent_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('item')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('ean')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('saleprice')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('retailerprice')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('retailersaleprice')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image'),
                TextColumn::make('view')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sale_count')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('tax')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_variable')
                    ->boolean(),
                TextColumn::make('variation')
                    ->searchable(),
                TextColumn::make('length')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('width')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('featured')
                    ->boolean(),
                TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('qrcode')
                    ->searchable(),
                TextColumn::make('order_no')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('pin')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ManageProducts::route('/'),
        ];
    }
}
