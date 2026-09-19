<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'seller_id', 'name', 'description', 'price', 'stock', 'image_path', 'variations'];

    /**
     * variations is stored as JSON, e.g. {"Color": ["Black", "White"], "Size": ["S", "M"]}
     * and read back as a PHP array.
     */
    protected function casts(): array
    {
        return [
            'variations' => 'array',
        ];
    }

    /**
     * Public URL for the product's image, or null if none uploaded yet.
     * image_path is stored relative to storage/app/public (e.g. "products/abc123.jpg"),
     * written there by the seller's upload form via Storage::disk('public')->put(...).
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    /** True when the buyer has to pick options (color, size, ...) before adding to cart. */
    public function hasVariations(): bool
    {
        return ! empty($this->variations);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
