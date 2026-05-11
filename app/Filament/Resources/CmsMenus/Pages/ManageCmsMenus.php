<?php

namespace App\Filament\Resources\CmsMenus\Pages;

use App\Filament\Resources\CmsMenus\CmsMenuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCmsMenus extends ManageRecords
{
    protected static string $resource = CmsMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
