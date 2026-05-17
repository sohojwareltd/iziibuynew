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
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColumnGroup;
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

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $globalSearchSort = 40;

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'sku', 'item', 'ean'];
    }

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
                RichEditor::make('details')
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('quantity')
                    ->numeric(),
                RichEditor::make('description')
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
                Section::make(__('Overview'))
                    ->icon(Heroicon::OutlinedCube)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->placeholder('-')
                            ->weight('bold')
                            ->columnSpanFull(),
                        TextEntry::make('sku')
                            ->label('SKU')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('slug')
                            ->placeholder('-'),
                        TextEntry::make('shop_id')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('parent_id')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('item')
                            ->placeholder('-'),
                        TextEntry::make('ean')
                            ->placeholder('-'),
                        IconEntry::make('status')
                            ->boolean()
                            ->placeholder('-'),
                        IconEntry::make('featured')
                            ->boolean()
                            ->placeholder('-'),
                        TextEntry::make('view')
                            ->numeric()
                            ->label(__('Views')),
                    ]),
                Section::make(__('Pricing & stock'))
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->columns(3)
                    ->schema([
                        TextEntry::make('price')
                            ->money('NOK')
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
                        TextEntry::make('quantity')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('sale_count')
                            ->numeric(),
                        TextEntry::make('tax')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('discount')
                            ->numeric()
                            ->placeholder('-'),
                    ]),
                Section::make(__('Content'))
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->columns(1)
                    ->collapsible()
                    ->schema([
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->prose()
                            ->columnSpanFull(),
                        TextEntry::make('details')
                            ->placeholder('-')
                            ->prose()
                            ->columnSpanFull(),
                        TextEntry::make('areas')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Media'))
                    ->icon(Heroicon::OutlinedPhoto)
                    ->schema([
                        ImageEntry::make('image')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('images')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make(__('Dimensions & variable product'))
                    ->icon(Heroicon::OutlinedSquare3Stack3d)
                    ->columns(3)
                    ->collapsible()
                    ->collapsed()
                    ->schema([
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
                        TextEntry::make('qrcode')
                            ->placeholder('-'),
                        TextEntry::make('order_no')
                            ->numeric()
                            ->placeholder('-'),
                        IconEntry::make('pin')
                            ->boolean(),
                    ]),
                Section::make(__('Timestamps'))
                    ->icon(Heroicon::OutlinedClock)
                    ->columns(2)
                    ->collapsed()
                    ->collapsible()
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('NOK')
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean(),
                IconColumn::make('featured')
                    ->boolean(),
                ColumnGroup::make(__('Identifiers'))
                    ->columns([
                        TextColumn::make('shop_id')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('parent_id')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('item')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('ean')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('slug')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true),
                    ]),
                ColumnGroup::make(__('Alternate pricing'))
                    ->columns([
                        TextColumn::make('saleprice')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('retailerprice')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('retailersaleprice')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('discount')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('tax')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                    ]),
                ColumnGroup::make(__('Stats'))
                    ->columns([
                        TextColumn::make('view')
                            ->label(__('Views'))
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('sale_count')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                    ]),
                ColumnGroup::make(__('Dimensions & variations'))
                    ->columns([
                        IconColumn::make('is_variable')
                            ->boolean()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('variation')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('length')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('width')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('height')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('weight')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                    ]),
                ColumnGroup::make(__('Other'))
                    ->columns([
                        TextColumn::make('qrcode')
                            ->searchable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('order_no')
                            ->numeric()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        IconColumn::make('pin')
                            ->boolean()
                            ->toggleable(isToggledHiddenByDefault: true),
                    ]),
                ColumnGroup::make(__('Timestamps'))
                    ->columns([
                        TextColumn::make('created_at')
                            ->dateTime()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                        TextColumn::make('updated_at')
                            ->dateTime()
                            ->sortable()
                            ->toggleable(isToggledHiddenByDefault: true),
                    ]),
            ])
            ->filters([
                //
            ])
            ->defaultSort('name')
            ->recordActions([
                ViewAction::make()
                    ->modalWidth('5xl')
                    ->modalHeading(fn (Product $record): string => $record->name ?? __('Product')),
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
