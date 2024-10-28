<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class CategoryController extends Controller
{
    public function addEditCategory(string $id): View
    {
        if ($id == 'new') {
            $category = false;
        } else {
            $category = Category::findOrFail($id);
        }
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.category-form', ['id' => $id])->with(compact(['category', 'categories']));
    }

    public function saveCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'categoryName' => 'required|string|max:255',
            'description' => 'required|string',
            'shortDescription' => 'required|string',
            'parentCategory' => 'nullable|exists:categories,id',
            'featureImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'thumbnailImage' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'iconImage' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'metaTitle' => 'nullable|string|max:255',
            'metaDescription' => 'nullable|string',
            'metaKeywords' => 'nullable|string|max:255',
            'metaUrl' => 'required|string|max:255',
        ]);

        // Determine if we are updating or creating a new category
        $categoryId = $request->input('id');

        $categoryData = [
            'name' => $validated['categoryName'],
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['shortDescription'] ?? null,
            'parent_id' => $validated['parentCategory'] ?? null,
            'meta_title' => $validated['metaTitle'] ?? null,
            'meta_description' => $validated['metaDescription'] ?? null,
            'meta_keywords' => $validated['metaKeywords'] ?? null,
            'meta_url' => $validated['metaUrl'] ?? null,
        ];

        // Handle image uploads and set feature image
        $cModel = Category::find($categoryId) ?? new Category;
        if ($request->hasFile('featureImage')) {
            $categoryData['feature_image'] = $cModel->uploadImage($request, 'featureImage', $categoryId);
        }

        if ($request->hasFile('thumbnailImage')) {
            $categoryData['thumbnail_image'] = $cModel->uploadImage($request, 'thumbnailImage', $categoryId);
        }

        if ($request->hasFile('iconImage')) {
            $categoryData['icon_image'] = $cModel->uploadImage($request, 'iconImage', $categoryId);
        }

        $categoryData['is_feature'] = $request->is_feature == "on" ? 1 : 0;
        // Create or update the category
        // Category::updateOrCreate(['id' => $categoryId], $categoryData);
        $cModel->fill($categoryData);
        $cModel->save();

        return redirect()->route('categories')->with('success', 'Category saved successfully.');
    }

    public function fetchSubcategories($parentCategoryId)
    {
        $subcategories = Category::where('parent_id', $parentCategoryId)->get(['id', 'name']);
        return response()->json($subcategories);
    }

    public function deleteCategory(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        // Remove image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('categories')->with('status', 'Category deleted successfully!');
    }

    // Get all categories
    public function getCategories()
    {
        try {
            // Load categories with nested relationships
            $categories = Category::with([
                'children.children.products',
                'children.products',
                'parent',
                'products'
            ])
                ->whereNull('parent_id')
                ->withCount('products')
                ->get();

            // Decode additional_images for each product
            $decodeAdditionalImages = function ($category) use (&$decodeAdditionalImages) {
                // Decode additional_images for the top-level category's products
                foreach ($category->products as $product) {
                    if (isset($product->additional_images)) {
                        $product->additional_images = json_decode($product->additional_images, true);
                    }
                }
                // Recursively decode additional_images for the children's products
                foreach ($category->children as $child) {
                    $decodeAdditionalImages($child);
                }
            };

            // Apply the decoding function to each category
            $categories->each(function ($category) use ($decodeAdditionalImages) {
                $decodeAdditionalImages($category);
            });
            // Add products count to children recursively
            $cModel = new Category();
            $categories->each(function ($category) use ($cModel) {
                $cModel->addProductsCountToChildren($category);
            });

            $countProductsRecursively = function ($category) use (&$countProductsRecursively) {
                $totalProductCount = $category->products_count;

                foreach ($category->children as $child) {
                    $totalProductCount += $countProductsRecursively($child);
                }

                // Add the total product count to the category
                $category->total_product_count = $totalProductCount;
                return $totalProductCount;
            };

            $categories->each(function ($category) use ($countProductsRecursively) {
                $countProductsRecursively($category);
            });
            $message = $categories->isEmpty() ? 'There are currently no categories available.' : '';

            $categoryCount = Category::whereNull('parent_id')->count();

            return response()->json([
                'status' => 'success',
                'categories' => $categories,
                'message' => $message,
                'length' => $categoryCount
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve categories.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCategory($id)
    {
        try {
            $category = Category::with([
                'children.children.products',
                'children.products',
                'products'
            ])
                ->withCount('products')
                ->whereNull('parent_id')  // Ensure it's a top-level category
                ->findOrFail($id);

            // Decode additional_images for each product
            $decodeAdditionalImages = function ($category) use (&$decodeAdditionalImages) {
                foreach ($category->products as $product) {
                    if (isset($product->additional_images)) {
                        $product->additional_images = json_decode($product->additional_images, true);
                    }
                }

                foreach ($category->children as $child) {
                    $decodeAdditionalImages($child);
                }
            };

            // Apply the decoding function to the category
            $decodeAdditionalImages($category);

            // Merge products from children into all_products
            $cModel = new Category();
            $allProducts = $category->products->merge($cModel->getAllChildrenProducts($category));

            $category->all_products = $allProducts->toArray();

            // Add products count to children recursively
            $cModel->addProductsCountToChildren($category);
            // Count top-level categories
            $categoryCount = Category::whereNull('parent_id')->count();

            return response()->json([
                'status' => 'success',
                'category' => $category,
                'length' => $categoryCount,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found.',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve Category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getSubcategory($id)
    {
        try {
            $subcategory = Category::with([
                'parent',
                'products'
            ])
                ->withCount('products')
                ->whereNotNull('parent_id')  // Ensure it's a subcategory
                ->findOrFail($id);

            // Decode additional_images for each product
            foreach ($subcategory->products as $product) {
                if (isset($product->additional_images)) {
                    $product->additional_images = json_decode($product->additional_images, true);
                }
            }
            // Load parent category
            $subcategory->load('parent');

            // Count subcategories
            $subcategoryCount = Category::whereNotNull('parent_id')->count();

            return response()->json([
                'status' => 'success',
                'subcategory' => $subcategory,
                'length' => $subcategoryCount,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subcategory not found.',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve Subcategory.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFeaturedCategories()
    {
        try {
            // Load featured categories with nested relationships
            $featuredCategories = Category::with([
                'children.children.products',
                'children.products',
                'parent',
                'products'
            ])
                ->withCount('products')
                ->where('is_feature', true)
                ->get();

            // Add products count to children recursively
            $cModel = new Category();
            $featuredCategories->each(function ($category) use ($cModel) {
                $cModel->addProductsCountToChildren($category);
            });

            $categoryCount = Category::where('is_feature', true)->count();

            $message = $featuredCategories->isEmpty() ? 'There are currently no categories available.' : '';

            return response()->json([
                'status' => 'success',
                'featuredCategory' => $featuredCategories,
                'message' => $message,
                'length' => $categoryCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve featured categories.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
