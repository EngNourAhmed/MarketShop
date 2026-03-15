<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'description',
        'description_ar',
        'description_en',
        'slug',
        'sku',
        'price',
        'unit_price',
        'quantity',
        'image',
        'images',
        'category',
        'category_id',
        'featured',
        'color',
        'colors',
        'size',
        'sizes',
        'added_by',
        'updated_by',
    ];

    protected $casts = [
        'images' => 'array',
        'sizes' => 'array',
        'colors' => 'array',
    ];

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier_prices')
            ->withPivot('price', 'unit_price', 'quantity')
            ->withTimestamps();
    }

    public function pricingTiers()
    {
        return $this->hasMany(ProductPricingTier::class)->orderBy('min_quantity');
    }

    public function supplierPricingTiers()
    {
        return $this->hasMany(ProductPricingTier::class)->whereNotNull('supplier_id')->orderBy('min_quantity');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProductComment::class);
    }
}
