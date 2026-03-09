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
            if (! Schema::hasColumn('quote_items', 'item_name')) {
                $table->string('item_name')->nullable()->after('name');
            }

            if (! Schema::hasColumn('quote_items', 'item_code')) {
                $table->string('item_code')->nullable()->after('sku');
            }

            if (! Schema::hasColumn('quote_items', 'unit')) {
                $table->string('unit')->nullable()->after('quantity');
            }

            if (! Schema::hasColumn('quote_items', 'unit_price')) {
                $table->decimal('unit_price', 12, 4)->default(0)->after('price');
            }

            if (! Schema::hasColumn('quote_items', 'line_subtotal')) {
                $table->decimal('line_subtotal', 12, 4)->default(0)->after('tax_amount');
            }

            if (! Schema::hasColumn('quote_items', 'line_total')) {
                $table->decimal('line_total', 12, 4)->default(0)->after('line_subtotal');
            }

            if (! Schema::hasColumn('quote_items', 'sort_order')) {
                $table->integer('sort_order')->nullable()->after('line_total');
            }

            if (Schema::hasColumn('quote_items', 'description')) {
                $table->text('description')->nullable()->change();
            }

            if (Schema::hasColumn('quote_items', 'product_id')) {
                $table->unsignedInteger('product_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep backward-compatible columns in place.
    }
};
