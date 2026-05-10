<?php

namespace App\Filament\Resources\RetailerEarnings\Pages;

use App\Filament\Resources\RetailerEarnings\RetailerEarningResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageRetailerEarnings extends ManageRecords
{
    protected static string $resource = RetailerEarningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
