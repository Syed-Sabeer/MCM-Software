<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_quotes', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_quotes', 'billing_address')) {
                $table->json('billing_address')->nullable()->after('person_id');
            }

            if (! Schema::hasColumn('vendor_quotes', 'shipping_address')) {
                $table->json('shipping_address')->nullable()->after('billing_address');
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'billing_address')) {
                $table->json('billing_address')->nullable()->after('organization_id');
            }

            if (! Schema::hasColumn('purchase_orders', 'shipping_address')) {
                $table->json('shipping_address')->nullable()->after('billing_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_quotes', function (Blueprint $table) {
            foreach (['billing_address', 'shipping_address'] as $column) {
                if (Schema::hasColumn('vendor_quotes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            foreach (['billing_address', 'shipping_address'] as $column) {
                if (Schema::hasColumn('purchase_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
