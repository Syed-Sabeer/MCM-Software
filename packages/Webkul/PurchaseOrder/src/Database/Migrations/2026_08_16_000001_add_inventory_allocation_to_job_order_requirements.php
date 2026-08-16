<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Services\MaterialInventoryService;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('job_order_requirements', 'inventory_allocated_qty')) {
            Schema::table('job_order_requirements', function (Blueprint $table) {
                $table->decimal('inventory_allocated_qty', 12, 4)
                    ->default(0)
                    ->after('required_qty');
            });
        }

        if (! Schema::hasTable('material_inventories') || ! Schema::hasTable('material_inventory_transactions')) {
            return;
        }

        $inventoryService = app(MaterialInventoryService::class);

        DB::table('job_orders')
            ->orderByDesc('id')
            ->pluck('id')
            ->each(function ($jobOrderId) use ($inventoryService) {
                $jobOrder = JobOrder::query()->with('requirements')->find($jobOrderId);

                if ($jobOrder) {
                    $inventoryService->syncJobOrder($jobOrder);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('job_order_requirements', 'inventory_allocated_qty')) {
            Schema::table('job_order_requirements', function (Blueprint $table) {
                $table->dropColumn('inventory_allocated_qty');
            });
        }
    }
};
