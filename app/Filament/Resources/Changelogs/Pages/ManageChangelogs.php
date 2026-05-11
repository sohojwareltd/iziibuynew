<?php

namespace App\Filament\Resources\Changelogs\Pages;

use App\Filament\Resources\Changelogs\ChangelogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageChangelogs extends ManageRecords
{
    protected static string $resource = ChangelogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
