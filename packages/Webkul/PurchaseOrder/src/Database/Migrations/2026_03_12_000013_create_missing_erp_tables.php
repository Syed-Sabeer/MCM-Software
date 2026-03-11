<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_order_items')) {
            Schema::create('job_order_items', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('job_order_id');
                $table->unsignedBigInteger('proforma_invoice_item_id')->nullable();
                $table->unsignedInteger('product_id')->nullable();
                $table->string('item_name');
                $table->string('item_code')->nullable();
                $table->text('description')->nullable();
                $table->decimal('qty', 12, 4)->default(0);
                $table->string('unit', 100)->nullable();
                $table->decimal('unit_price', 12, 4)->nullable();
                $table->decimal('line_total', 12, 4)->nullable();
                $table->integer('sort_order')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('job_cards')) {
            Schema::create('job_cards', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('job_order_id');
                $table->unsignedInteger('job_order_item_id')->nullable();
                $table->unsignedInteger('product_id')->nullable();
                $table->string('title')->nullable();
                $table->string('status', 50)->default('open');
                $table->text('remarks')->nullable();
                $table->unsignedInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('job_card_sections')) {
            Schema::create('job_card_sections', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('job_card_id');
                $table->unsignedInteger('source_product_section_id')->nullable();
                $table->string('section_name');
                $table->integer('sort_order')->nullable();
                $table->string('status', 50)->default('not_started');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('job_card_section_items')) {
            Schema::create('job_card_section_items', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('job_card_section_id');
                $table->unsignedInteger('source_product_section_item_id')->nullable();
                $table->string('name');
                $table->decimal('qty', 12, 4)->nullable();
                $table->string('unit', 100)->nullable();
                $table->integer('sort_order')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('job_order_requirements')) {
            Schema::create('job_order_requirements', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('job_order_id');
                $table->unsignedInteger('job_order_item_id')->nullable();
                $table->unsignedInteger('product_id')->nullable();
                $table->string('material_name');
                $table->string('unit', 100)->nullable();
                $table->decimal('qty_per_unit', 12, 4)->default(0);
                $table->decimal('ordered_qty', 12, 4)->default(0);
                $table->decimal('required_qty', 12, 4)->default(0);
                $table->decimal('received_qty', 12, 4)->default(0);
                $table->decimal('balance_qty', 12, 4)->default(0);
                $table->string('status', 50)->default('pending');
                $table->integer('sort_order')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('goods_receipts')) {
            Schema::create('goods_receipts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('goods_receipt_number')->unique();
                $table->unsignedInteger('purchase_order_id');
                $table->unsignedInteger('vendor_id')->nullable();
                $table->date('receipt_date');
                $table->unsignedInteger('received_by')->nullable();
                $table->text('notes')->nullable();
                $table->string('attachment_path')->nullable();
                $table->string('status', 50)->default('posted');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('goods_receipt_items')) {
            Schema::create('goods_receipt_items', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('goods_receipt_id');
                $table->unsignedInteger('purchase_order_item_id');
                $table->unsignedInteger('requirement_id')->nullable();
                $table->string('material_name');
                $table->decimal('received_qty', 12, 4)->default(0);
                $table->string('unit', 100)->nullable();
                $table->decimal('unit_price', 12, 4)->default(0);
                $table->decimal('line_total', 12, 4)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('vendor_payables')) {
            Schema::create('vendor_payables', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('purchase_order_id')->nullable();
                $table->unsignedInteger('goods_receipt_id')->nullable();
                $table->unsignedInteger('organization_id');
                $table->string('payable_number')->nullable()->unique();
                $table->date('payable_date');
                $table->decimal('total_amount', 12, 4)->default(0);
                $table->decimal('paid_amount', 12, 4)->default(0);
                $table->decimal('remaining_amount', 12, 4)->default(0);
                $table->string('status', 50)->default('open');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        foreach (['vendor_payables', 'goods_receipt_items', 'goods_receipts', 'job_order_requirements', 'job_card_section_items', 'job_card_sections', 'job_cards', 'job_order_items'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
