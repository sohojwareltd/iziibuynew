<?php

declare(strict_types=1);

namespace App\Services\Cms;

use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Resources\Resource;

final class FilamentResourceRegistry
{
    /**
     * @return array<string, string> class => label
     */
    public function resourceOptions(): array
    {
        $panel = Filament::getPanel('admin');

        $options = [];

        foreach ($panel->getResources() as $resourceClass) {
            if (! is_subclass_of($resourceClass, Resource::class)) {
                continue;
            }

            $options[$resourceClass] = $resourceClass::getNavigationLabel();
        }

        asort($options);

        return $options;
    }

    /**
     * @return array<string, string> class => label
     */
    public function pageOptions(): array
    {
        $panel = Filament::getPanel('admin');

        $options = [];

        foreach ($panel->getPages() as $pageClass) {
            if (! is_subclass_of($pageClass, Page::class)) {
                continue;
            }

            if (! $pageClass::shouldRegisterNavigation()) {
                continue;
            }

            $options[$pageClass] = $pageClass::getNavigationLabel();
        }

        asort($options);

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public function navigationGroupOptions(): array
    {
        $groups = [];

        foreach (Filament::getPanel('admin')->getNavigationGroups() as $key => $group) {
            $label = is_string($group) ? $group : $group->getLabel();
            $groups[is_string($key) ? $key : (string) $key] = (string) $label;
        }

        return $groups;
    }
}
