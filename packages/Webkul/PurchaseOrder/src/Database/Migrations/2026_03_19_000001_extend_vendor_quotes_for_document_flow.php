<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_quotes', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_quotes', 'payment_term')) {
                $table->string('payment_term')->nullable()->after('expected_response_date');
            }

            if (! Schema::hasColumn('vendor_quotes', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('payment_term');
            }

            if (! Schema::hasColumn('vendor_quotes', 'first_delivery_date')) {
                $table->date('first_delivery_date')->nullable()->after('shipping_method');
            }

            if (! Schema::hasColumn('vendor_quotes', 'last_delivery_date')) {
                $table->date('last_delivery_date')->nullable()->after('first_delivery_date');
            }

            if (! Schema::hasColumn('vendor_quotes', 'terms')) {
                $table->text('terms')->nullable()->after('notes');
            }

            if (! Schema::hasColumn('vendor_quotes', 'subtotal')) {
                $table->decimal('subtotal', 12, 4)->default(0)->after('terms');
            }

            if (! Schema::hasColumn('vendor_quotes', 'sales_tax_percent')) {
                $table->decimal('sales_tax_percent', 12, 4)->default(0)->after('subtotal');
            }

            if (! Schema::hasColumn('vendor_quotes', 'sales_tax_amount')) {
                $table->decimal('sales_tax_amount', 12, 4)->default(0)->after('sales_tax_percent');
            }

            if (! Schema::hasColumn('vendor_quotes', 'freight')) {
                $table->decimal('freight', 12, 4)->default(0)->after('sales_tax_amount');
            }

            if (! Schema::hasColumn('vendor_quotes', 'grand_total')) {
                $table->decimal('grand_total', 12, 4)->default(0)->after('freight');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_quotes', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('vendor_quotes', 'payment_term') ? 'payment_term' : null,
                Schema::hasColumn('vendor_quotes', 'shipping_method') ? 'shipping_method' : null,
                Schema::hasColumn('vendor_quotes', 'first_delivery_date') ? 'first_delivery_date' : null,
                Schema::hasColumn('vendor_quotes', 'last_delivery_date') ? 'last_delivery_date' : null,
                Schema::hasColumn('vendor_quotes', 'terms') ? 'terms' : null,
                Schema::hasColumn('vendor_quotes', 'subtotal') ? 'subtotal' : null,
                Schema::hasColumn('vendor_quotes', 'sales_tax_percent') ? 'sales_tax_percent' : null,
                Schema::hasColumn('vendor_quotes', 'sales_tax_amount') ? 'sales_tax_amount' : null,
                Schema::hasColumn('vendor_quotes', 'freight') ? 'freight' : null,
                Schema::hasColumn('vendor_quotes', 'grand_total') ? 'grand_total' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
