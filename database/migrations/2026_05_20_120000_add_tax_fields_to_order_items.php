<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_minor')->default(0)->after('unit_price_minor');
            $table->foreignId('tax_classification_id')->nullable()->after('discount_minor')
                ->constrained('tax_classifications')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_classification_id');
            $table->dropColumn('discount_minor');
        });
    }
};
