<?php

namespace App\Filament\Resources\CmsMenus;

use App\Filament\Resources\CmsMenus\Pages\ManageCmsMenus;
use App\Models\CmsMenu;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CmsMenuResource extends Resource
{
    protected static ?string $model = CmsMenu::class;

    protected static string|\UnitEnum|null $navigationGroup = 'site';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Menus';

    protected static ?string $modelLabel = 'menu';

    protected static ?string $pluralModelLabel = 'menus';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('location')
                    ->maxLength(255)
                    ->placeholder('header, footer, …')
                    ->label(__('Location')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('location'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ManageCmsMenus::route('/'),
        ];
    }
}
