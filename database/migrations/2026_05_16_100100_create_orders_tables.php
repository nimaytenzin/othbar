<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('country_name')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->unsignedBigInteger('total_minor')->default(0);
            $table->string('currency_code', 8)->default('BTN');
            $table->string('status', 32)->default('new');
            $table->string('payment_status', 32)->default('pending');
            $table->string('shipping_status', 32)->default('unsent');
            $table->text('notes')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_access_token', 64)->nullable();
            $table->string('fulfillment_method', 32)->default('delivery');
            $table->json('metadata')->nullable();
            $table->foreignId('shipping_address_id')->nullable()->constrained('order_addresses')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price_minor');
            $table->string('sku')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_addresses');
    }
};
