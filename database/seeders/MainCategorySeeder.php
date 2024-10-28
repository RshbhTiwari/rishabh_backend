<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MainCategory;
use App\Models\Category;
use App\Models\SubCategory;
class MainCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MainCategory::factory(5)->create()->each(function ($mainCategory) {
            // Create categories
            Category::factory(10)->create(['main_category_id' => $mainCategory->id])->each(function ($category) {
                // Create subcategories
                SubCategory::factory(3)->create(['category_id' => $category->id]);
            });
        });//
    }
}
