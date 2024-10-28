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
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('billing_address_id')->nullable()->after('customer_id');
            $table->unsignedBigInteger('shipping_address_id')->nullable()->after('billing_address_id');

            // Add foreign key constraints
            $table->foreign('billing_address_id')->references('id')->on('customer_addresses')->onDelete('set null');
            $table->foreign('shipping_address_id')->references('id')->on('customer_addresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['billing_address_id']);
            $table->dropForeign(['shipping_address_id']);
            $table->dropColumn('billing_address_id');
            $table->dropColumn('shipping_address_id');
        });
    }
};
