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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('name');
            $table->string('contact');
            $table->string('landmarkname')->nullable();
            $table->string('addressname');
            $table->string('pincode');
            $table->string('locality');
            $table->string('state');
            $table->string('city');
            $table->string('addresstype');
            $table->string('email')->nullable();
            $table->boolean('defaultaddress')->default(false);
            $table->boolean('is_billing')->default(false);
            $table->boolean('is_shipping')->default(false);
            $table->boolean('default_billing_address')->default(false);
            $table->boolean('default_shipping_address')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade'); // assuming your customers are in the users table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
