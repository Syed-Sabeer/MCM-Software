<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_order_requirements', function (Blueprint $table) {
            if (! Schema::hasColumn('job_order_requirements', 'item_codes')) {
                $table->text('item_codes')->nullable()->after('product_id');
            }

            if (! Schema::hasColumn('job_order_requirements', 'material_reference_id')) {
                $table->unsignedBigInteger('material_reference_id')->nullable()->after('item_codes');
            }

            if (! Schema::hasColumn('job_order_requirements', 'vendor_ids')) {
                $table->json('vendor_ids')->nullable()->after('unit');
            }

            if (! Schema::hasColumn('job_order_requirements', 'color_name')) {
                $table->string('color_name')->nullable()->after('vendor_ids');
            }

            if (! Schema::hasColumn('job_order_requirements', 'color_code')) {
                $table->string('color_code', 20)->nullable()->after('color_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_order_requirements', function (Blueprint $table) {
            foreach (['item_codes', 'material_reference_id', 'vendor_ids', 'color_name', 'color_code'] as $column) {
                if (Schema::hasColumn('job_order_requirements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
