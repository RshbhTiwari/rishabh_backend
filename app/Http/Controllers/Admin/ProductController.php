<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function addEditProduct(string $id): View
    {
        $product = $id === 'new' ? new Product() : Product::findOrFail($id);
        $categories = Category::get()->pluck('name', 'id')->toArray();
        $attributList = Attribute::get()->pluck('name', 'id')->toArray();
        $variantList = $product->variants()
            ->get()
            ->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'attribute_id' => $variant->attribute_id,
                    'value' => $variant->variant_value,
                    'price' => $variant->variant_price,
                    'stock' => $variant->variant_stock,
                ];
            });

        return view('admin.product-form', ['id' => $id])->with(compact(['product', 'categories', 'attributList', 'variantList']));
    }

    public function saveProduct(Request $request): RedirectResponse
    {

        // Validation rules
        $validatedData = $request->validate([
            'productName' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discountPrice' => 'nullable|numeric|lt:price', // Discount must be less than price
            'productDescription' => 'required|string',
            'short_description' => 'required|string',
            'brand' => 'required|string|max:100',
            'stockQuantity' => 'nullable|integer|min:0',
            'lowStockThreshold' => 'nullable|integer|min:0',
            'stockStatus' => 'required|string|in:in_stock,out_of_stock,backorder',
            'category_ids' => 'required|array',
            'category_ids.*' => 'integer|exists:categories,id', // Ensure each category id exists
            'is_feature' => 'nullable|boolean',
            'metaTitle' => 'nullable|string|max:255',
            'metaDescription' => 'nullable|string|max:255',
            'metaKeywords' => 'nullable|string|max:255',
            'metaUrl' => 'required',
            'sku' => 'required|string|max:255|unique:products,sku,' . $request->id,
            'featureImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the feature image
            'additionalImages.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate additional images

            // Variants validation
            'variants' => 'nullable|array',
            'variants.*.attribute_id' => 'required|integer|exists:attributes,id',
            'variants.*.value' => 'required|string|max:255',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.discount' => 'nullable|numeric|min:0',
            'variants.*.extension' => 'required|numeric|min:0',
        ]);




        // Prepare product data for saving
        $data = [
            'name' => $validatedData['productName'],
            'price' => $validatedData['price'],
            'discount_price' => $validatedData['discountPrice'] ?? null,
            'description' => $validatedData['productDescription'],
            'short_description' => $validatedData['short_description'],
            'brand' => $validatedData['brand'],
            'stock_quantity' => $validatedData['stockQuantity'],
            'low_stock_threshold' => $validatedData['lowStockThreshold'],
            'stock_status' => $validatedData['stockStatus'],
            'category_ids' => implode(',', $validatedData['category_ids']),
            'is_feature' => $request->has('is_feature'),
            'meta_title' => $validatedData['metaTitle'] ?? null,
            'meta_description' => $validatedData['metaDescription'] ?? null,
            'meta_keywords' => $validatedData['metaKeywords'] ?? null,
            'meta_url' => $validatedData['metaUrl'] ?? null,
            'sku' => $validatedData['sku'] ?? null,
        ];

        if ($request->hasFile('featureImage')) {
            $imagePath = $request->file('featureImage')->store('images', 'public');
            $data['image'] = $imagePath;

            if ($request->id && $product = Product::find($request->id)) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
            }
        }

        if ($request->hasFile('additionalImages')) {
            $additionalImages = [];
            foreach ($request->file('additionalImages') as $file) {
                $path = $file->store('images', 'public');
                $additionalImages[] = $path;
            }
            $data['additional_images'] = json_encode($additionalImages);
        }

        if ($request->is_feature == "on") {
            $data['is_feature'] = 1;
        } else {
            $data['is_feature'] = 0;
        }

        $message = $request->id ? 'Product updated' : 'New product added';
        $product = Product::updateOrCreate(['id' => $request->id], $data);

        $variants = array_filter($request->variants, function ($variant) {
            // Filter out if any of the values are null
            return !is_null($variant['attribute_id']) &&
                !is_null($variant['value']) &&
                !is_null($variant['stock']) &&
                !is_null($variant['price']);
        });

        $requestVariantIds = collect($request->variants)->pluck('id')->filter()->toArray();
        // Delete old variants that are not in the request
        Product::where('parent_id', $product->id)
            ->whereNotIn('id', $requestVariantIds)
            ->delete();

        if (!is_null($request->variants)) {
            foreach ($request->variants as $variant) {
                $variantData = array_merge(
                    array_diff_key($data, ['sku' => '']),
                    [
                        'is_variant' => 1,
                        'parent_id' => $product->id,
                        'variant_stock' => $variant['stock'],
                        'variant_price' => $variant['price'],
                        'variant_discount' => $variant['discount'],
                        'variant_extension' => $variant['extension'],
                        'variant_value' => $variant['value'],
                        'attribute_id' => $variant['attribute_id'],

                    ]
                );
                if (isset($variant['id'])) {
                    Product::where('id', $variant['id'])->update($variantData);
                } else {
                    Product::create($variantData);
                }
            }
        }
        return redirect()->route('products')->with('status', $message . ' successfully!');
    }

    // public function getProducts()
    // {
    //     try {
    //         $products = Product::with('variants')->where('is_variant', '0')->get();
    //         // Attach categories to each product
    //         $products->each(function ($product) {
    //             $product->additional_images = json_decode($product->additional_images);
    //             $categoryIds = explode(',', $product->category_ids);
    //             $product->categories = Category::whereIn('id', $categoryIds)->get();
    //         });

    //         return response()->json([
    //             'status' => 'success',
    //             'products' => $products
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to retrieve products.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function getProducts()
    // {
    //     try {
    //         // Fetch main products (not variants) with relationships
    //         $products = Product::with('variants')->where('is_variant', '0')->get();

    //         // Modify each product's structure to match desired response
    //         $products->each(function ($product) {
    //             $product->additional_images = json_decode($product->additional_images);

    //             // Attach categories
    //             $categoryIds = explode(',', $product->category_ids);
    //             $product->categories = Category::whereIn('id', $categoryIds)->get();

    //             // Set the variant data structure
    //             if ($product->variants->isNotEmpty()) {
    //                 $product->is_variant = "true";

    //                 // Unique attribute IDs from product variants
    //                 $attributeIds = $product->variants->pluck('attribute_id')->unique();

    //                 // Retrieve attributes for variant names
    //                 $attributes = Attribute::whereIn('id', $attributeIds)->get();

    //                 // Build variant_name
    //                 $product->variant_name = $attributes->map(function ($attribute) {
    //                     return [
    //                         'id' => $attribute->id,
    //                         'variant' => $attribute->name
    //                     ];
    //                 });

    //                 // Build variant_value with multiple_values array
    //                 $product->variant_value = $attributes->map(function ($attribute) use ($product) {
    //                     // Filter variants related to the current attribute
    //                     $relatedVariants = $product->variants->filter(function ($variant) use ($attribute) {
    //                         return $variant->attribute_id == $attribute->id;
    //                     });

    //                     // Map multiple_values for each variant of the attribute
    //                     $multipleValues = $relatedVariants->map(function ($variant) {
    //                         return [
    //                             'id' => $variant->id,
    //                             'size' => $variant->variant_value,
    //                             'price' => $variant->variant_price,
    //                             'discount_price' => $variant->variant_discount
    //                         ];
    //                     });

    //                     return [
    //                         'id' => $attribute->id,
    //                         'multiple_values' => $multipleValues,
    //                         'variant' => $attribute->name
    //                     ];
    //                 });
    //             } else {
    //                 // If no variants, mark accordingly
    //                 $product->is_variant = "false";
    //                 $product->variant_name = [];
    //                 $product->variant_value = [];
    //             }

    //             // Remove the variants array from the response
    //             unset($product->variants);
    //         });

    //         // Return the final JSON response
    //         return response()->json([
    //             'status' => 'success',
    //             'products' => $products
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to retrieve products.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getProducts()
{
    try {
        // Fetch main products (not variants) with relationships
        $products = Product::with('variants')->where('is_variant', '0')->get();

        // Modify each product's structure to match desired response
        $products->each(function ($product) {
            $product->additional_images = json_decode($product->additional_images);

            // Attach categories
            $categoryIds = explode(',', $product->category_ids);
            $product->categories = Category::whereIn('id', $categoryIds)->get();

            // Set the variant data structure
            if ($product->variants->isNotEmpty()) {
                $product->is_variant = "true";

                // Unique attribute IDs from product variants
                $attributeIds = $product->variants->pluck('attribute_id')->unique();

                // Retrieve attributes for variant names
                $attributes = Attribute::whereIn('id', $attributeIds)->get();

                // Build variant_name
                $product->variant_name = $attributes->map(function ($attribute) {
                    return [
                        'id' => $attribute->id,
                        'variant' => $attribute->name
                    ];
                });

                // Build variant_value with multiple_values array
                $product->variant_value = $attributes->map(function ($attribute) use ($product) {
                    // Filter variants related to the current attribute
                    $relatedVariants = $product->variants->filter(function ($variant) use ($attribute) {
                        return $variant->attribute_id == $attribute->id;
                    });

                    // Map multiple_values for each variant of the attribute as a sequential array
                    $multipleValues = $relatedVariants->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'size' => $variant->variant_value,
                            'price' => $variant->variant_price,
                            'discount_price' => $variant->variant_discount
                        ];
                    })->values(); // Use ->values() to ensure it’s a sequential array

                    return [
                        'id' => $attribute->id,
                        'multiple_values' => $multipleValues,
                        'variant' => $attribute->name
                    ];
                })->values(); // Ensure that variant_value itself is also a sequential array
            } else {
                // If no variants, mark accordingly
                $product->is_variant = "false";
                $product->variant_name = [];
                $product->variant_value = [];
            }

            // Remove the variants array from the response
            unset($product->variants);
        });

        // Return the final JSON response
        return response()->json([
            'status' => 'success',
            'products' => $products
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve products.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    // public function getProduct($id)
    // {
    //     try {
    //         $product = Product::findOrFail($id);
    //         // Attach categories to the product
    //         $product->additional_images = json_decode($product->additional_images);
    //         $categoryIds = explode(',', $product->category_ids);
    //         $product->categories = Category::whereIn('id', $categoryIds)->get();

    //         return response()->json([
    //             'status' => 'success',
    //             'product' => $product
    //         ], 200);
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Product not found.',
    //             'error' => $e->getMessage()
    //         ], 404);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Failed to retrieve the product.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getProduct($id)
    {
        try {
            // Fetch the product by ID along with its variants
            $product = Product::with('variants')->findOrFail($id);
    
            // Decode additional_images JSON field
            $product->additional_images = json_decode($product->additional_images);
    
            // Attach categories to the product
            $categoryIds = explode(',', $product->category_ids);
            $product->categories = Category::whereIn('id', $categoryIds)->get();
    
            // Set up the variant data structure
            if ($product->variants->isNotEmpty()) {
                $product->is_variant = "true";
    
                // Get unique attribute IDs from variants
                $attributeIds = $product->variants->pluck('attribute_id')->unique();
    
                // Fetch attributes for variant names
                $attributes = Attribute::whereIn('id', $attributeIds)->get();
    
                // Build variant_name
                $product->variant_name = $attributes->map(function ($attribute) {
                    return [
                        'id' => $attribute->id,
                        'variant' => $attribute->name
                    ];
                });
    
                // Build variant_value with multiple_values array
                $product->variant_value = $attributes->map(function ($attribute) use ($product) {
                    // Filter variants for the current attribute
                    $relatedVariants = $product->variants->filter(function ($variant) use ($attribute) {
                        return $variant->attribute_id == $attribute->id;
                    });
    
                    // Map multiple_values for each variant under the attribute as a sequential array
                    $multipleValues = $relatedVariants->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'size' => $variant->variant_value,
                            'price' => $variant->variant_price,
                            'discount_price' => $variant->variant_discount
                        ];
                    })->values(); // Ensures `multiple_values` is sequential
    
                    return [
                        'id' => $attribute->id,
                        'multiple_values' => $multipleValues,
                        'variant' => $attribute->name
                    ];
                })->values(); // Ensures `variant_value` is also sequential
            } else {
                // If no variants, mark as "false"
                $product->is_variant = "false";
                $product->variant_name = [];
                $product->variant_value = [];
            }
    
            // Remove the variants array from the final response
            unset($product->variants);
    
            // Return the structured response
            return response()->json([
                'status' => 'success',
                'product' => $product
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve the product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function deleteProduct(string $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        // Delete the image from storage
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products')->with('status', 'Product deleted successfully!');
    }
}
