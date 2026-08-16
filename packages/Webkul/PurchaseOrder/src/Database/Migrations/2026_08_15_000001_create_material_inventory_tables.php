<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('material_inventories')) {
            Schema::create('material_inventories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('material_reference_id')->unique();
                $table->decimal('on_hand', 16, 4)->default(0);
                $table->decimal('average_unit_cost', 16, 4)->default(0);
                $table->decimal('reorder_level', 16, 4)->default(0);
                $table->timestamps();

                $table->foreign('material_reference_id')
                    ->references('id')
                    ->on('material_references')
                    ->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('material_inventory_transactions')) {
            Schema::create('material_inventory_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('material_inventory_id');
                $table->unsignedBigInteger('material_reference_id');
                $table->string('type', 60);
                $table->decimal('quantity', 16, 4);
                $table->decimal('unit_cost', 16, 4)->default(0);
                $table->decimal('total_value', 18, 4)->default(0);
                $table->decimal('balance_after', 16, 4);
                $table->decimal('average_cost_after', 16, 4)->default(0);
                $table->string('reference_type', 60)->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->unsignedBigInteger('reference_line_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('occurred_at');
                $table->unsignedInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['material_reference_id', 'occurred_at'], 'material_inventory_tx_material_date_idx');
                $table->index(['reference_type', 'reference_id'], 'material_inventory_tx_reference_idx');
                $table->foreign('material_inventory_id')
                    ->references('id')
                    ->on('material_inventories')
                    ->cascadeOnDelete();
                $table->foreign('material_reference_id')
                    ->references('id')
                    ->on('material_references')
                    ->cascadeOnDelete();
                $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('material_inventory_transactions');
        Schema::dropIfExists('material_inventories');
    }
};
