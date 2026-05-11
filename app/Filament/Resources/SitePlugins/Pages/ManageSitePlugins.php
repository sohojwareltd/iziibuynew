<?php

namespace App\Filament\Resources\SitePlugins\Pages;

use App\Filament\Resources\SitePlugins\SitePluginResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSitePlugins extends ManageRecords
{
    protected static string $resource = SitePluginResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
