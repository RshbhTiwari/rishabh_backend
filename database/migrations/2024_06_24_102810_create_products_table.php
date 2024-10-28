<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->text('short_description');
            $table->string('brand')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->nullable();
            $table->integer('low_stock_threshold')->nullable();
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock');
            $table->string('category_ids')->nullable();
            $table->string('image');
            $table->json('additional_images');
            $table->boolean('is_feature')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('meta_url')->nullable();
            $table->boolean('is_variant')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->string('sku')->unique()->nullable();
            $table->integer('variant_stock')->nullable();
            $table->decimal('variant_price', 10, 2)->nullable();
            $table->decimal('variant_discount', 10, 2)->nullable();
            $table->string('variant_extension')->nullable();
            $table->string('variant_value')->nullable();
            $table->foreignId('attribute_id')->nullable()->constrained('attributes')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
