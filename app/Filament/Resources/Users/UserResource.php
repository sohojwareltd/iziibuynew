<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\UnitEnum|null $navigationGroup = 'people';

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('last_name'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('tax_id'),
                TextInput::make('shop_id')
                    ->numeric(),
                Select::make('role_id')
                    ->relationship('role', 'name'),
                TextInput::make('avatar')
                    ->default('users/default.png'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('pt_package_id')
                    ->numeric(),
                TextInput::make('pt_trainer_id')
                    ->numeric(),
                TextInput::make('pt_package_price')
                    ->numeric()
                    ->prefix('$'),
                Textarea::make('pt_package_purchase_history')
                    ->columnSpanFull(),
                Toggle::make('pt_free_tier')
                    ->required(),
                TextInput::make('service_type')
                    ->required()
                    ->default('both'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('last_name')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('tax_id')
                    ->placeholder('-'),
                TextEntry::make('shop_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('role.name')
                    ->label('Role')
                    ->placeholder('-'),
                TextEntry::make('avatar')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('pt_package_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('pt_trainer_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('pt_package_price')
                    ->money()
                    ->placeholder('-'),
                TextEntry::make('pt_package_purchase_history')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('pt_free_tier')
                    ->boolean(),
                TextEntry::make('service_type'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('tax_id')
                    ->searchable(),
                TextColumn::make('shop_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('role.name')
                    ->searchable(),
                TextColumn::make('avatar')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pt_package_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pt_trainer_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pt_package_price')
                    ->money()
                    ->sortable(),
                IconColumn::make('pt_free_tier')
                    ->boolean(),
                TextColumn::make('service_type')
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
            'index' => ManageUsers::route('/'),
        ];
    }
}
