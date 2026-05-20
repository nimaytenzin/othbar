<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('business_type', 32)->nullable()->after('business_licence_number');
            $table->string('drc_registration_number', 50)->nullable()->after('business_type');
            $table->string('business_address_line1')->nullable()->after('drc_registration_number');
            $table->string('business_address_line2')->nullable()->after('business_address_line1');
            $table->string('business_city', 100)->nullable()->after('business_address_line2');
            $table->string('business_district', 100)->nullable()->after('business_city');
            $table->string('business_postal_code', 20)->nullable()->after('business_district');
            $table->string('business_phone', 50)->nullable()->after('business_postal_code');
            $table->string('business_email')->nullable()->after('business_phone');
            $table->string('business_website')->nullable()->after('business_email');
            $table->string('business_logo_path')->nullable()->after('business_website');
            $table->string('default_currency', 8)->default('BTN')->after('business_logo_path');
            $table->unsignedTinyInteger('fiscal_year_start_month')->default(1)->after('default_currency');
            $table->unsignedSmallInteger('invoice_payment_terms_days')->default(30)->after('fiscal_year_start_month');
            $table->text('invoice_terms_text')->nullable()->after('invoice_payment_terms_days');
            $table->text('invoice_footer_text')->nullable()->after('invoice_terms_text');
            $table->boolean('is_gst_registered')->default(true)->after('invoice_footer_text');
            $table->foreignId('default_tax_classification_id')->nullable()->after('is_gst_registered')
                ->constrained('tax_classifications')->nullOnDelete();
            $table->string('prefix_invoice', 20)->default('INV')->after('default_tax_classification_id');
            $table->string('prefix_bill', 20)->default('BILL')->after('prefix_invoice');
            $table->string('prefix_customer_payment', 20)->default('RCP')->after('prefix_bill');
            $table->string('prefix_supplier_payment', 20)->default('SPR')->after('prefix_customer_payment');
            $table->string('prefix_quotation', 20)->default('QT')->after('prefix_supplier_payment');
            $table->string('prefix_contract', 20)->default('CTR')->after('prefix_quotation');
            $table->string('prefix_credit_note', 20)->default('CN')->after('prefix_contract');
            $table->string('prefix_debit_note', 20)->default('DN')->after('prefix_credit_note');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_tax_classification_id');
            $table->dropColumn([
                'business_type',
                'drc_registration_number',
                'business_address_line1',
                'business_address_line2',
                'business_city',
                'business_district',
                'business_postal_code',
                'business_phone',
                'business_email',
                'business_website',
                'business_logo_path',
                'default_currency',
                'fiscal_year_start_month',
                'invoice_payment_terms_days',
                'invoice_terms_text',
                'invoice_footer_text',
                'is_gst_registered',
                'prefix_invoice',
                'prefix_bill',
                'prefix_customer_payment',
                'prefix_supplier_payment',
                'prefix_quotation',
                'prefix_contract',
                'prefix_credit_note',
                'prefix_debit_note',
            ]);
        });
    }
};
