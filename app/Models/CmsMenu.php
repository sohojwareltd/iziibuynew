<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenu extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany<CmsMenuItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'cms_menu_id')->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * @return HasMany<CmsMenuItem, $this>
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'cms_menu_id')->orderBy('sort_order');
    }
}
