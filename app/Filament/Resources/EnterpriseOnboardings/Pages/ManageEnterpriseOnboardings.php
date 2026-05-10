<?php

namespace App\Filament\Resources\EnterpriseOnboardings\Pages;

use App\Filament\Resources\EnterpriseOnboardings\EnterpriseOnboardingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEnterpriseOnboardings extends ManageRecords
{
    protected static string $resource = EnterpriseOnboardingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
