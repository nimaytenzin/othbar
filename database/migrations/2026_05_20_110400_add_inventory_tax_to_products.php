<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('track_inventory')->default(false)->after('stock_quantity');
            $table->foreignId('tax_classification_id')->nullable()->after('track_inventory')
                ->constrained('tax_classifications')->nullOnDelete();
            $table->unsignedInteger('reorder_level')->nullable()->after('tax_classification_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tax_classification_id');
            $table->dropColumn(['track_inventory', 'reorder_level']);
        });
    }
};
