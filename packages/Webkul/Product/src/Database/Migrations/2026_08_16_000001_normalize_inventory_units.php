<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Product\Services\UnitConversionService;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Services\MaterialInventoryService;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('unit_references') || ! Schema::hasTable('material_references')) {
            return;
        }

        $converter = app(UnitConversionService::class);

        DB::table('product_consumptions')
            ->join('material_references', 'product_consumptions.material_reference_id', '=', 'material_references.id')
            ->select([
                'product_consumptions.id',
                'product_consumptions.qty',
                'product_consumptions.unit',
                'material_references.unit as material_unit',
            ])
            ->orderBy('product_consumptions.id')
            ->get()
            ->each(function ($consumption) use ($converter) {
                if (strcasecmp(trim((string) $consumption->unit), trim((string) $consumption->material_unit)) === 0) {
                    return;
                }

                $converted = $converter->convert(
                    (float) $consumption->qty,
                    (string) $consumption->unit,
                    (string) $consumption->material_unit
                );

                DB::table('product_consumptions')->where('id', $consumption->id)->update([
                    'qty' => round($converted ?? (float) $consumption->qty, 4),
                    'unit' => $consumption->material_unit,
                    'updated_at' => now(),
                ]);
            });

        if (Schema::hasTable('job_order_requirements')) {
            DB::table('job_order_requirements')
                ->join('material_references', 'job_order_requirements.material_reference_id', '=', 'material_references.id')
                ->select([
                    'job_order_requirements.*',
                    'material_references.unit as material_unit',
                ])
                ->orderBy('job_order_requirements.id')
                ->get()
                ->each(function ($requirement) use ($converter) {
                    if (strcasecmp(trim((string) $requirement->unit), trim((string) $requirement->material_unit)) === 0) {
                        return;
                    }

                    $factor = $converter->factor((string) $requirement->unit, (string) $requirement->material_unit) ?? 1.0;
                    $payload = [
                        'qty_per_unit' => round((float) $requirement->qty_per_unit * $factor, 4),
                        'ordered_qty' => round((float) $requirement->ordered_qty, 4),
                        'required_qty' => round((float) $requirement->required_qty * $factor, 4),
                        'received_qty' => round((float) $requirement->received_qty * $factor, 4),
                        'balance_qty' => round((float) $requirement->balance_qty * $factor, 4),
                        'unit' => $requirement->material_unit,
                        'updated_at' => now(),
                    ];

                    if (Schema::hasColumn('job_order_requirements', 'inventory_allocated_qty')) {
                        $payload['inventory_allocated_qty'] = round((float) $requirement->inventory_allocated_qty * $factor, 4);
                    }

                    DB::table('job_order_requirements')->where('id', $requirement->id)->update($payload);
                });
        }

        if (Schema::hasTable('material_inventories') && Schema::hasTable('material_inventory_transactions')) {
            $inventoryService = app(MaterialInventoryService::class);

            DB::table('job_orders')->orderByDesc('id')->pluck('id')->each(function ($jobOrderId) use ($inventoryService) {
                $jobOrder = JobOrder::query()->with('requirements')->find($jobOrderId);

                if ($jobOrder) {
                    $inventoryService->syncJobOrder($jobOrder);
                }
            });
        }
    }

    public function down(): void
    {
        // Unit normalization is intentionally irreversible.
    }
};
