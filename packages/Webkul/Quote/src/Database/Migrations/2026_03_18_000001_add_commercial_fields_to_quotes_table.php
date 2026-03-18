<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (! Schema::hasColumn('quotes', 'payment_term')) {
                $table->string('payment_term')->nullable()->after('terms');
            }

            if (! Schema::hasColumn('quotes', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('payment_term');
            }

            if (! Schema::hasColumn('quotes', 'production_time')) {
                $table->string('production_time')->nullable()->after('shipping_method');
            }

            if (! Schema::hasColumn('quotes', 'transit_time')) {
                $table->string('transit_time')->nullable()->after('production_time');
            }

            if (! Schema::hasColumn('quotes', 'etd')) {
                $table->date('etd')->nullable()->after('transit_time');
            }

            if (! Schema::hasColumn('quotes', 'eta')) {
                $table->date('eta')->nullable()->after('etd');
            }

            if (! Schema::hasColumn('quotes', 'tariff_percent')) {
                $table->decimal('tariff_percent', 12, 4)->default(0)->after('discount_percent');
            }

            if (! Schema::hasColumn('quotes', 'freight_percent')) {
                $table->decimal('freight_percent', 12, 4)->default(0)->after('tariff_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $dropColumns = array_filter([
                Schema::hasColumn('quotes', 'payment_term') ? 'payment_term' : null,
                Schema::hasColumn('quotes', 'shipping_method') ? 'shipping_method' : null,
                Schema::hasColumn('quotes', 'production_time') ? 'production_time' : null,
                Schema::hasColumn('quotes', 'transit_time') ? 'transit_time' : null,
                Schema::hasColumn('quotes', 'etd') ? 'etd' : null,
                Schema::hasColumn('quotes', 'eta') ? 'eta' : null,
                Schema::hasColumn('quotes', 'tariff_percent') ? 'tariff_percent' : null,
                Schema::hasColumn('quotes', 'freight_percent') ? 'freight_percent' : null,
            ]);

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};