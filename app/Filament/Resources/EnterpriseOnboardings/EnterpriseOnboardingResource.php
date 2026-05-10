<?php

namespace App\Filament\Resources\EnterpriseOnboardings;

use App\Filament\Resources\EnterpriseOnboardings\Pages\ManageEnterpriseOnboardings;
use App\Models\EnterpriseOnboarding;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnterpriseOnboardingResource extends Resource
{
    protected static ?string $model = EnterpriseOnboarding::class;

    protected static string|\UnitEnum|null $navigationGroup = 'onboarding';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Enterprise onboarding';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('key'),
                TextInput::make('company_name')
                    ->required(),
                TextInput::make('company_email')
                    ->email()
                    ->required(),
                TextInput::make('company_address')
                    ->required(),
                TextInput::make('company_registration')
                    ->required(),
                TextInput::make('company_domain')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_establishment')
                    ->required(),
                TextInput::make('establishment_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('last_paid_at'),
                TextInput::make('paymentMethod')
                    ->required()
                    ->default('quickpay'),
                TextInput::make('api'),
                TextInput::make('contract_url')
                    ->url(),
                TextInput::make('contract_signed')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('contract_status')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('kyc_status')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('company_name')
                    ->searchable(),
                TextColumn::make('company_email')
                    ->searchable(),
                TextColumn::make('company_address')
                    ->searchable(),
                TextColumn::make('company_registration')
                    ->searchable(),
                TextColumn::make('company_domain')
                    ->searchable(),
                TextColumn::make('status')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fee')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_establishment')
                    ->boolean(),
                TextColumn::make('establishment_fee')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('last_paid_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('paymentMethod')
                    ->searchable(),
                TextColumn::make('api')
                    ->searchable(),
                TextColumn::make('contract_url')
                    ->searchable(),
                TextColumn::make('contract_signed')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contract_status')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kyc_status')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => ManageEnterpriseOnboardings::route('/'),
        ];
    }
}
