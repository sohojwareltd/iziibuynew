<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Enums\MenuContext;
use App\Models\CmsMenu as CmsMenuModel;
use App\Models\CmsMenuItem;
use App\Services\Cms\MenuTreeBuilder;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class CmsMenu extends Component
{
    /**
     * @var Collection<int, array{item: CmsMenuItem, url: ?string, children: Collection}>
     */
    public Collection $tree;

    public bool $hasMenu;

    public function __construct(
        public ?string $slug = null,
        public ?string $location = null,
        public string $itemClass = 'nav-item',
        public string $linkClass = 'nav-item nav-link',
        public bool $fallback = true,
    ) {
        $treeBuilder = app(MenuTreeBuilder::class);
        $menu = $this->resolveMenu();

        $this->hasMenu = $menu !== null && $menu->allItems()->where('is_active', true)->exists();
        $this->tree = $menu ? $treeBuilder->build($menu) : collect();
    }

    public function render(): View|Closure|string
    {
        return view('components.cms-menu');
    }

    private function resolveMenu(): ?CmsMenuModel
    {
        $query = CmsMenuModel::query()
            ->forContext(MenuContext::Frontend)
            ->active();

        if (filled($this->slug)) {
            return $query->where('slug', $this->slug)->first();
        }

        if (filled($this->location)) {
            return $query->where('location', $this->location)->first();
        }

        return $query->where('location', 'header')->first()
            ?? $query->orderBy('id')->first();
    }
}
