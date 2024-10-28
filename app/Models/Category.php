<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'main_category_id',
        'simple_image',
        'thumbnail_image',
        'icon_image',
        'feature_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_feature',
        'parent_id',
        'meta_url'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_ids')->where('is_variant', '0');
    }

    public function variants()
    {
        return $this->hasMany(Product::class, 'category_ids')->where('is_variant', '1');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function addProductsCountToChildren($category)
    {
        if ($category->children) {
            $category->children->each(function ($child) {
                $child->products_count = $child->products->count();
                $this->addProductsCountToChildren($child); // Recursively load children
            });
        }
    }

    public function getAllChildrenProducts($category)
    {
        $allProducts = collect([]);

        foreach ($category->children as $child) {
            // Add products of this child category
            $allProducts = $allProducts->merge($child->products);

            // Recursively add products of grandchildren
            foreach ($child->children as $grandchild) {
                $allProducts = $allProducts->merge($grandchild->products);
            }
        }
        return $allProducts;
    }

    // Helper function to handle image uploads
    public function uploadImage(Request $request, string $imageField, ?int $categoryId): ?string
    {
        if ($request->hasFile($imageField)) {
            $category = $categoryId ? Category::find($categoryId) : null;
            if ($category && $category->$imageField) {
                Storage::disk('public')->delete($category->$imageField);
            }
            return $request->file($imageField)->store('images', 'public');
        }
        return $categoryId ? Category::find($categoryId)->$imageField : null;
    }



}
