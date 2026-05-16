<?php

declare(strict_types=1);

namespace App\Services\Cms;

use App\Enums\MenuLinkType;
use App\Models\CmsMenuItem;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Route;

final class MenuUrlResolver
{
    public function resolve(CmsMenuItem $item): ?string
    {
        return match ($item->link_type) {
            MenuLinkType::Route => $this->resolveRoute($item->route_name),
            MenuLinkType::Resource => $this->resolveResource($item->resource_class),
            MenuLinkType::Url => $this->resolveUrl($item->url),
        };
    }

    public function resolveRoute(?string $routeName): ?string
    {
        if (blank($routeName) || ! Route::has($routeName)) {
            return null;
        }

        try {
            return route($routeName);
        } catch (\Throwable) {
            return null;
        }
    }

    public function resolveResource(?string $resourceClass): ?string
    {
        if (blank($resourceClass) || ! class_exists($resourceClass)) {
            return null;
        }

        if (! is_subclass_of($resourceClass, Resource::class)) {
            return null;
        }

        try {
            return $resourceClass::getUrl();
        } catch (\Throwable) {
            return null;
        }
    }

    public function resolveUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '//')) {
            return $url;
        }

        $path = ltrim($url, '/');

        if (str_starts_with($path, 'admin/')) {
            $path = 'panel/'.substr($path, strlen('admin/'));
        }

        return url('/'.$path);
    }
}
