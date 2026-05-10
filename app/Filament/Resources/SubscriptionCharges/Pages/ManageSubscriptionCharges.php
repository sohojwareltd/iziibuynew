<?php

namespace App\Filament\Resources\SubscriptionCharges\Pages;

use App\Filament\Resources\SubscriptionCharges\SubscriptionChargeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSubscriptionCharges extends ManageRecords
{
    protected static string $resource = SubscriptionChargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
