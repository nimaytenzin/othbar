<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number', 80);
            $table->string('branch')->nullable();
            $table->string('swift_or_code', 50)->nullable();
            $table->string('qr_path')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->foreignId('default_bank_account_id')->nullable()->after('prefix_debit_note')
                ->constrained('bank_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_bank_account_id');
        });

        Schema::dropIfExists('bank_accounts');
    }
};
