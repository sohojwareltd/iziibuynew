<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cms\LegacyAdminMenuSynchronizer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cms:sync-admin-menu')]
#[Description('Link the legacy Voyager admin sidebar menu to Filament panel navigation')]
class SyncAdminCmsMenuCommand extends Command
{
    public function handle(LegacyAdminMenuSynchronizer $synchronizer): int
    {
        $menu = $synchronizer->sync();

        $this->components->info(sprintf(
            'Synced admin menu "%s" (%d items).',
            $menu->name,
            $menu->allItems()->count(),
        ));

        return self::SUCCESS;
    }
}
