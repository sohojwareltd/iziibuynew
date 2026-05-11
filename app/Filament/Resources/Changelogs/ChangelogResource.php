<?php

namespace App\Filament\Resources\Changelogs;

use App\Filament\Resources\Changelogs\Pages\ManageChangelogs;
use App\Models\Changelog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChangelogResource extends Resource
{
    protected static ?string $model = Changelog::class;

    protected static string|\UnitEnum|null $navigationGroup = 'site';

    protected static ?int $navigationSort = 31;

    protected static ?string $navigationLabel = 'Changelog';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('version')
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(4),
                TextInput::make('large_change')
                    ->maxLength(255),
                TextInput::make('type')
                    ->maxLength(255)
                    ->placeholder('feature, fix, …'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('version')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('large_change')
                    ->limit(40),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => ManageChangelogs::route('/'),
        ];
    }
}
