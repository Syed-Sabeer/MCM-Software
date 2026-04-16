<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_consumptions', function (Blueprint $table) {
            if (! Schema::hasColumn('product_consumptions', 'material_reference_id')) {
                $table->unsignedBigInteger('material_reference_id')->nullable()->after('product_id');
            }

            if (! Schema::hasColumn('product_consumptions', 'vendor_ids')) {
                $table->json('vendor_ids')->nullable()->after('unit');
            }

            if (! Schema::hasColumn('product_consumptions', 'color_name')) {
                $table->string('color_name')->nullable()->after('vendor_ids');
            }

            if (! Schema::hasColumn('product_consumptions', 'color_code')) {
                $table->string('color_code', 20)->nullable()->after('color_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_consumptions', function (Blueprint $table) {
            foreach (['material_reference_id', 'vendor_ids', 'color_name', 'color_code'] as $column) {
                if (Schema::hasColumn('product_consumptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
