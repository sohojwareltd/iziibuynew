<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsTo<PostCategory, $this>
     */
    public function postCategory(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }
}
