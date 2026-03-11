<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'job_order_id')) {
                $table->unsignedInteger('job_order_id')->nullable()->after('job_number');
                $table->foreign('job_order_id')->references('id')->on('job_orders')->onDelete('set null');
            }

            if (! Schema::hasColumn('purchase_orders', 'vendor_quote_id')) {
                $table->unsignedInteger('vendor_quote_id')->nullable()->after('job_order_id');
                $table->foreign('vendor_quote_id')->references('id')->on('vendor_quotes')->onDelete('set null');
            }

            if (! Schema::hasColumn('purchase_orders', 'expected_receive_date')) {
                $table->date('expected_receive_date')->nullable()->after('last_delivery_date');
            }

            if (! Schema::hasColumn('purchase_orders', 'status')) {
                $table->string('status', 50)->default('draft')->after('expected_receive_date');
            }

            if (! Schema::hasColumn('purchase_orders', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('notes');
            }

            if (! Schema::hasColumn('purchase_orders', 'closed_at')) {
                $table->timestamp('closed_at')->nullable()->after('attachment_path');
            }

            if (! Schema::hasColumn('purchase_orders', 'closed_by')) {
                $table->unsignedInteger('closed_by')->nullable()->after('closed_at');
                $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_order_items', 'requirement_id')) {
                $table->unsignedInteger('requirement_id')->nullable()->after('purchase_order_id');
                $table->foreign('requirement_id')->references('id')->on('job_order_requirements')->onDelete('set null');
            }

            if (! Schema::hasColumn('purchase_order_items', 'product_id')) {
                $table->unsignedInteger('product_id')->nullable()->after('requirement_id');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            }

            if (! Schema::hasColumn('purchase_order_items', 'material_name')) {
                $table->string('material_name')->nullable()->after('item');
            }

            if (! Schema::hasColumn('purchase_order_items', 'ordered_quantity')) {
                $table->decimal('ordered_quantity', 12, 4)->default(0)->after('quantity');
            }

            if (! Schema::hasColumn('purchase_order_items', 'received_quantity')) {
                $table->decimal('received_quantity', 12, 4)->default(0)->after('ordered_quantity');
            }

            if (! Schema::hasColumn('purchase_order_items', 'pending_quantity')) {
                $table->decimal('pending_quantity', 12, 4)->default(0)->after('received_quantity');
            }

            if (! Schema::hasColumn('purchase_order_items', 'unit')) {
                $table->string('unit', 100)->nullable()->after('pending_quantity');
            }

            if (! Schema::hasColumn('purchase_order_items', 'expected_receive_date')) {
                $table->date('expected_receive_date')->nullable()->after('unit');
            }

            if (! Schema::hasColumn('purchase_order_items', 'line_status')) {
                $table->string('line_status', 50)->default('open')->after('expected_receive_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            foreach (['requirement_id', 'product_id', 'material_name', 'ordered_quantity', 'received_quantity', 'pending_quantity', 'unit', 'expected_receive_date', 'line_status'] as $column) {
                if (Schema::hasColumn('purchase_order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            foreach (['job_order_id', 'vendor_quote_id', 'expected_receive_date', 'status', 'attachment_path', 'closed_at', 'closed_by'] as $column) {
                if (Schema::hasColumn('purchase_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
