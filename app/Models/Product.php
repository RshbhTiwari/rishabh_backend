<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'price',
        'discount_price',
        'category_ids',
        'brand',
        'stock_quantity',
        'low_stock_threshold',
        'stock_status',
        'image',
        'additional_images',
        'is_feature',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_url',
        'sku',
        'is_variant',
        'parent_id',
        'variant_stock',
        'variant_price',
        'variant_value',
        'attribute_id',
        'variant_discount',
        'variant_extension'

    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

}
