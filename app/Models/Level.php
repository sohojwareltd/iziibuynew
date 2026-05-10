<?php

namespace App\Models;

use App\Models\Traits\LegacyVoyagerGetsTranslatedAttribute;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Level extends Model
{
    use HasFactory, HasTranslations, LegacyVoyagerGetsTranslatedAttribute;

    protected $translatable = ['title', 'details'];

    protected $guarded = [];

    protected $bonus = [50 => 10, 70 => 20, 90 => 30, 110 => 40];

    public function bonus(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => empty((array) json_decode($value)) ? $this->bonus : (array) json_decode($value),
            set: fn ($value) => json_encode($value)
        );
    }
}
