<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MenuContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenu extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'context' => MenuContext::class,
            'is_active' => 'boolean',
            'replaces_panel_navigation' => 'boolean',
        ];
    }

    /**
     * @return HasMany<CmsMenuItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'cms_menu_id')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * @return HasMany<CmsMenuItem, $this>
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'cms_menu_id')->orderBy('sort_order');
    }

    /**
     * @param  Builder<CmsMenu>  $query
     * @return Builder<CmsMenu>
     */
    public function scopeForContext(Builder $query, MenuContext $context): Builder
    {
        return $query->where('context', $context);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
