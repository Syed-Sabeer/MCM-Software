<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('proforma_invoices', 'payment_term')) {
                $table->string('payment_term')->nullable()->after('terms');
            }
        });

        Schema::table('proforma_invoice_items', function (Blueprint $table) {
            if (! Schema::hasColumn('proforma_invoice_items', 'color_variant_id')) {
                $table->unsignedBigInteger('color_variant_id')->nullable()->after('product_id');
            }

            if (! Schema::hasColumn('proforma_invoice_items', 'color_variant_name')) {
                $table->string('color_variant_name')->nullable()->after('color_variant_id');
            }

            if (! Schema::hasColumn('proforma_invoice_items', 'preview_image')) {
                $table->text('preview_image')->nullable()->after('color_variant_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('proforma_invoice_items', function (Blueprint $table) {
            foreach (['preview_image', 'color_variant_name', 'color_variant_id'] as $column) {
                if (Schema::hasColumn('proforma_invoice_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('proforma_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('proforma_invoices', 'payment_term')) {
                $table->dropColumn('payment_term');
            }
        });
    }
};
