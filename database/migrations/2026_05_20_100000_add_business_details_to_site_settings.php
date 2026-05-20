<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('gst_percentage');
            $table->string('gst_tpn', 50)->nullable()->after('business_name');
            $table->string('business_licence_number', 80)->nullable()->after('gst_tpn');
        });

        DB::table('site_settings')->where('id', 1)->update([
            'business_name' => 'Othbar processing and packaging',
            'gst_tpn' => 'TBB29438',
            'business_licence_number' => 'Lic.1054845',
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'gst_tpn', 'business_licence_number']);
        });
    }
};
