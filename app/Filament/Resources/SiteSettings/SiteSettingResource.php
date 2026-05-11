<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|\UnitEnum|null $navigationGroup = 'site';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $modelLabel = 'setting';

    protected static ?string $pluralModelLabel = 'settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label(__('Key')),
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Textarea::make('value')
                    ->columnSpanFull()
                    ->rows(4),
                Select::make('type')
                    ->options([
                        'text' => __('Single line'),
                        'textarea' => __('Multi line'),
                        'image' => __('Image path / URL'),
                        'checkbox' => __('On / off (stored as 0 or 1)'),
                    ])
                    ->required()
                    ->default('text'),
                TextInput::make('group_name')
                    ->default('general')
                    ->maxLength(255)
                    ->label(__('Group')),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('label')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('group_name')
                    ->label(__('Group'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('group_name')
                    ->label(__('Section'))
                    ->collapsible(),
            ])
            ->defaultGroup('group_name')
            ->defaultSort('sort_order')
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
            'index' => ManageSiteSettings::route('/'),
        ];
    }
}
