<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('proforma_invoices', 'converted_to_invoice_id')) {
                $table->unsignedBigInteger('converted_to_invoice_id')->nullable()->after('source_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('proforma_invoices', 'converted_to_invoice_id')) {
                $table->dropColumn('converted_to_invoice_id');
            }
        });
    }
};
