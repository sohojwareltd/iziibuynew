<?php

declare(strict_types=1);

namespace App\Services\Cms;

use App\Models\CmsMenu;
use App\Models\CmsMenuItem;
use Illuminate\Support\Collection;

final class MenuTreeBuilder
{
    public function __construct(
        private readonly MenuUrlResolver $urlResolver,
    ) {}

    /**
     * @return Collection<int, array{item: CmsMenuItem, url: ?string, children: Collection}>
     */
    public function build(CmsMenu $menu, bool $onlyActive = true): Collection
    {
        $query = $menu->allItems();

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        $items = $query->get();

        return $this->buildFromFlatCollection($items);
    }

    /**
     * @param  Collection<int, CmsMenuItem>  $items
     * @return Collection<int, array{item: CmsMenuItem, url: ?string, children: Collection}>
     */
    public function buildFromFlatCollection(Collection $items, ?int $parentId = null): Collection
    {
        return $items
            ->where('parent_id', $parentId)
            ->sortBy('sort_order')
            ->values()
            ->map(fn (CmsMenuItem $item): array => [
                'item' => $item,
                'url' => $this->urlResolver->resolve($item),
                'children' => $this->buildFromFlatCollection($items, $item->id),
            ]);
    }
}
