<?php

namespace App\Filament\Resources\RetailerMetas;

use App\Filament\Pages\RetailerReportPage;
use App\Filament\Pages\RetailerWithdrawalsPage;
use App\Filament\Resources\RetailerMetas\Pages\CreateRetailer;
use App\Filament\Resources\RetailerMetas\Pages\EditRetailerMeta;
use App\Filament\Resources\RetailerMetas\Pages\ManageRetailerMetas;
use App\Models\RetailerMeta;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RetailerMetaResource extends Resource
{
    protected static ?string $model = RetailerMeta::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|\UnitEnum|null $navigationGroup = 'retailers';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Retailers';

    protected static ?string $modelLabel = 'Retailer';

    protected static ?string $pluralModelLabel = 'Retailers';

    public static function getRecordTitle(?Model $record): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        if (! $record instanceof RetailerMeta) {
            return null;
        }

        return $record->user?->email ?? (string) $record->getKey();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'retailerType']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tax')
                    ->numeric()
                    ->nullable(),
                TextInput::make('tax_number')
                    ->nullable(),
                TextInput::make('bank_account_number')
                    ->nullable(),
                TextInput::make('qr')
                    ->label('QR path')
                    ->nullable(),
                Select::make('type')
                    ->relationship('retailerType', 'label')
                    ->required(),
                Select::make('parent_id')
                    ->label('Parent user')
                    ->relationship('parent', 'email')
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.email')
                    ->label('Email'),
                TextEntry::make('user.name')
                    ->label('First name'),
                TextEntry::make('user.last_name')
                    ->label('Last name'),
                TextEntry::make('tax')
                    ->placeholder('-'),
                TextEntry::make('tax_number')
                    ->placeholder('-'),
                TextEntry::make('bank_account_number')
                    ->placeholder('-'),
                TextEntry::make('retailerType.label')
                    ->label('Type')
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('qr')
                    ->label('QR')
                    ->disk(config('iziibuy.storage_disk', config('filesystems.default', 'public')))
                    ->height(48)
                    ->square()
                    ->defaultImageUrl(url('/favicon.ico')),
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('user.phone')
                    ->label('Phone')
                    ->searchable(),
                TextColumn::make('retailerType.label')
                    ->label('Type'),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                Action::make('report')
                    ->label('Report')
                    ->icon(Heroicon::OutlinedChartBar)
                    ->url(fn (RetailerMeta $record): string => RetailerReportPage::getUrl(['user' => $record->user_id])),
                Action::make('withdrawals')
                    ->label('Withdrawals')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->url(fn (RetailerMeta $record): string => RetailerWithdrawalsPage::getUrl().'?user='.$record->user_id),
                EditAction::make(),
                DeleteAction::make()
                    ->using(function (Model $record): bool {
                        if (! $record instanceof RetailerMeta) {
                            return false;
                        }

                        $user = $record->user;
                        $record->delete();
                        $user?->delete();

                        return true;
                    }),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRetailerMetas::route('/'),
            'create' => CreateRetailer::route('/create'),
            'edit' => EditRetailerMeta::route('/{record}/edit'),
        ];
    }
}
