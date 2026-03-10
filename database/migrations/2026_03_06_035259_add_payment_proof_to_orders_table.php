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
        Schema::table('sh_orders', function (Blueprint $table) {
            $table->string('payment_proof_path')->nullable()->after('notes');
            $table->string('payment_reference')->nullable()->after('payment_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('sh_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_proof_path', 'payment_reference']);
        });
    }
};
