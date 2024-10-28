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
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('type')->nullable(); // 'billing' or 'shipping'
            $table->string('locality');
            $table->string('addrestype')->nullable();
            $table->string('addressname')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('landmarkname');
            $table->string('contact');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};
