<?php

namespace App\Filament\Resources\CmsMenuItems\Pages;

use App\Filament\Resources\CmsMenuItems\CmsMenuItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCmsMenuItems extends ManageRecords
{
    protected static string $resource = CmsMenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
