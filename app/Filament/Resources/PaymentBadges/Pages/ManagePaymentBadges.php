<?php

namespace App\Filament\Resources\PaymentBadges\Pages;

use App\Filament\Resources\PaymentBadges\PaymentBadgeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePaymentBadges extends ManageRecords
{
    protected static string $resource = PaymentBadgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
