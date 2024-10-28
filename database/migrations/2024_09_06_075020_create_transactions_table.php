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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade')->unique();
            $table->string('payment_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('INR');
            $table->boolean('international')->default(false);
            $table->string('method')->nullable();
            $table->decimal('amount_refunded', 10, 2)->default(0);
            $table->string('payment_status')->nullable();
            $table->boolean('captured')->default(false);
            $table->string('description')->nullable();
            $table->longText('card_details')->nullable();
            $table->string('bank')->nullable();
            $table->string('wallet')->nullable();
            $table->string('vpa')->nullable();
            $table->string('token_id')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->string('error_code')->nullable();
            $table->string('error_description')->nullable();
            $table->string('error_source')->nullable();
            $table->string('error_step')->nullable();
            $table->string('error_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
