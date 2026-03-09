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
        Schema::table('quote_items', function (Blueprint $table) {
            if (! Schema::hasColumn('quote_items', 'color_variant_id')) {
                $table->unsignedBigInteger('color_variant_id')->nullable()->after('product_id');
            }

            if (! Schema::hasColumn('quote_items', 'color_variant_name')) {
                $table->string('color_variant_name')->nullable()->after('color_variant_id');
            }

            if (! Schema::hasColumn('quote_items', 'preview_image')) {
                $table->string('preview_image')->nullable()->after('color_variant_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            if (Schema::hasColumn('quote_items', 'preview_image')) {
                $table->dropColumn('preview_image');
            }

            if (Schema::hasColumn('quote_items', 'color_variant_name')) {
                $table->dropColumn('color_variant_name');
            }

            if (Schema::hasColumn('quote_items', 'color_variant_id')) {
                $table->dropColumn('color_variant_id');
            }
        });
    }
};
