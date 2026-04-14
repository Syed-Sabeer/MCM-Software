<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            if (! Schema::hasColumn('product_colors', 'cost_price')) {
                $table->decimal('cost_price', 12, 4)->nullable()->after('color_code');
            }

            if (! Schema::hasColumn('product_colors', 'selling_price')) {
                $table->decimal('selling_price', 12, 4)->nullable()->after('cost_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_colors', function (Blueprint $table) {
            if (Schema::hasColumn('product_colors', 'selling_price')) {
                $table->dropColumn('selling_price');
            }

            if (Schema::hasColumn('product_colors', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
        });
    }
};
