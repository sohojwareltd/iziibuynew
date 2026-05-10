<?php

namespace App\Filament\Resources\RetailerMetas\Pages;

use App\Filament\Pages\RetailerWithdrawalsPage;
use App\Filament\Resources\RetailerMetas\RetailerMetaResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageRetailerMetas extends ManageRecords
{
    protected static string $resource = RetailerMetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('withdrawalsOverview')
                ->label('Withdrawals overview')
                ->icon(Heroicon::OutlinedBanknotes)
                ->url(RetailerWithdrawalsPage::getUrl()),
            CreateAction::make(),
        ];
    }
}
