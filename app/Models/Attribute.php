<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends Model
{
    use HasFactory;
    protected $table = 'attributes';
    protected $guarded = [];

    // ---------------------------------------------------------------- Relationships

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withPivot(['is_filter', 'is_variation']);
    }

    public function values()
    {
        return $this->hasMany(ProductAttribute::class)->select('attribute_id', 'value')->distinct();
    }

    public function variationValues()
    {
        return $this->hasMany(ProductVariation::class)->select('attribute_id', 'value')->distinct();
    }

    // Relationships ----------------------------------------------------------------
}
