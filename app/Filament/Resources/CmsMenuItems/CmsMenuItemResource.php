<?php

namespace App\Filament\Resources\CmsMenuItems;

use App\Filament\Resources\CmsMenuItems\Pages\ManageCmsMenuItems;
use App\Filament\Resources\CmsMenuItems\Pages\MenuBuilder;
use App\Models\CmsMenuItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CmsMenuItemResource extends Resource
{
    protected static ?string $model = CmsMenuItem::class;

    protected static string|\UnitEnum|null $navigationGroup = 'site';

    protected static ?int $navigationSort = 21;

    protected static ?string $navigationLabel = 'Menu items';

    protected static ?string $modelLabel = 'menu item';

    protected static ?string $pluralModelLabel = 'menu items';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cms_menu_id')
                    ->relationship('menu', 'name')
                    ->required()
                    ->preload()
                    ->label(__('Menu')),
                Select::make('parent_id')
                    ->relationship('parent', 'title', modifyQueryUsing: fn ($query) => $query->orderBy('sort_order'))
                    ->searchable()
                    ->preload()
                    ->label(__('Parent item')),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->maxLength(2048)
                    ->label(__('URL or path')),
                TextInput::make('route_name')
                    ->maxLength(255)
                    ->label(__('Route name')),
                TextInput::make('icon')
                    ->maxLength(255)
                    ->placeholder('heroicon-o-home'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Toggle::make('open_new_tab')
                    ->label(__('Open in new tab')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menu.name')
                    ->label(__('Menu'))
                    ->sortable(),
                TextColumn::make('parent.title')
                    ->label(__('Parent')),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('url')
                    ->limit(40),
                TextColumn::make('route_name')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('open_new_tab')
                    ->boolean(),
            ])
            ->recordActions([
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
            'index' => ManageCmsMenuItems::route('/'),
            'menu-builder' => MenuBuilder::route('/menu-builder'),
        ];
    }
}
