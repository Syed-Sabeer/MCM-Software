<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\PurchaseOrder\MaterialInventoryDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Models\Organization;
use Webkul\Product\Models\MaterialReference;
use Webkul\Product\Models\UnitReference;
use Webkul\PurchaseOrder\Models\MaterialInventory;
use Webkul\PurchaseOrder\Models\MaterialInventoryTransaction;
use Webkul\PurchaseOrder\Services\MaterialInventoryService;

class MaterialInventoryController extends Controller
{
    public function __construct(protected MaterialInventoryService $inventoryService)
    {
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(MaterialInventoryDataGrid::class)->process();
        }

        $totals = $this->inventoryTotals();
        $materials = MaterialReference::query()->orderBy('name')->get(['id', 'name', 'unit']);
        $units = UnitReference::query()->orderBy('name')->get(['name']);
        $vendors = Organization::query()
            ->whereIn('type', ['vendor', 'Vendor'])
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Organization $vendor) => [
                'id' => (int) $vendor->id,
                'name' => (string) ($vendor->name ?: 'Vendor '.$vendor->id),
            ])
            ->values();
        $recentTransactions = MaterialInventoryTransaction::query()
            ->with(['material:id,name,unit', 'creator:id,name'])
            ->latest('occurred_at')
            ->latest('id')
            ->limit(8)
            ->get();

        return view('admin::inventory.index', compact('totals', 'materials', 'units', 'vendors', 'recentTransactions'));
    }

    public function edit(int $materialId): JsonResponse
    {
        $material = MaterialReference::query()->findOrFail($materialId);
        $inventory = MaterialInventory::query()->firstOrCreate(
            ['material_reference_id' => $material->id],
            ['on_hand' => 0, 'average_unit_cost' => 0, 'reorder_level' => 0]
        );

        return response()->json([
            'data' => [
                'material_reference_id' => $material->id,
                'material_name' => $material->name,
                'unit' => $material->unit,
                'on_hand' => (float) $inventory->on_hand,
                'average_unit_cost' => (float) $inventory->average_unit_cost,
            ],
        ]);
    }

    public function view(int $materialId): View
    {
        $material = MaterialReference::query()->findOrFail($materialId);
        $inventory = MaterialInventory::query()->firstOrCreate(
            ['material_reference_id' => $material->id],
            ['on_hand' => 0, 'average_unit_cost' => 0, 'reorder_level' => 0]
        );
        $transactions = MaterialInventoryTransaction::query()
            ->with('creator:id,name')
            ->where('material_reference_id', $material->id)
            ->latest('occurred_at')
            ->latest('id')
            ->paginate(20);
        $materials = collect([$material]);

        return view('admin::inventory.view', compact('material', 'inventory', 'transactions', 'materials'));
    }

    public function storeMovement(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'material_reference_id' => ['required', 'exists:material_references,id'],
            'type' => ['required', Rule::in(array_keys(MaterialInventoryService::MANUAL_TYPES))],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['nullable', 'numeric', 'min:0', 'required_if:type,stock_in,set_balance'],
            'occurred_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'return_material_id' => ['nullable', 'exists:material_references,id'],
        ]);

        $transaction = $this->inventoryService->recordManual(
            (int) $data['material_reference_id'],
            $data['type'],
            (float) $data['quantity'],
            isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
            $data['notes'] ?? null,
            $data['occurred_at'],
            auth()->id()
        );

        if ($request->expectsJson()) {
            $totals = $this->inventoryTotals();

            return response()->json([
                'message' => 'Inventory updated successfully.',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'totals' => [
                        'material_count' => (int) $totals->material_count,
                        'stock_value' => core()->formatBasePrice($totals->stock_value, 2),
                        'low_stock_count' => (int) $totals->low_stock_count,
                        'out_of_stock_count' => (int) $totals->out_of_stock_count,
                    ],
                ],
            ]);
        }

        $redirect = ! empty($data['return_material_id'])
            ? route('admin.inventory.view', $data['return_material_id'])
            : route('admin.inventory.index');

        return redirect($redirect)->with('success', 'Inventory updated successfully.');
    }

    public function updateSettings(Request $request, int $materialId): RedirectResponse
    {
        MaterialReference::query()->findOrFail($materialId);
        $data = $request->validate(['reorder_level' => ['required', 'numeric', 'min:0']]);
        $this->inventoryService->updateReorderLevel($materialId, (float) $data['reorder_level']);

        return back()->with('success', 'Reorder level updated successfully.');
    }

    protected function inventoryTotals(): object
    {
        return DB::table('material_references')
            ->leftJoin('material_inventories', 'material_references.id', '=', 'material_inventories.material_reference_id')
            ->selectRaw('COUNT(material_references.id) as material_count')
            ->selectRaw('COALESCE(SUM(COALESCE(material_inventories.on_hand, 0) * COALESCE(material_inventories.average_unit_cost, 0)), 0) as stock_value')
            ->selectRaw('SUM(CASE WHEN COALESCE(material_inventories.on_hand, 0) <= 0 THEN 1 ELSE 0 END) as out_of_stock_count')
            ->selectRaw('SUM(CASE WHEN COALESCE(material_inventories.reorder_level, 0) > 0 AND COALESCE(material_inventories.on_hand, 0) > 0 AND material_inventories.on_hand <= material_inventories.reorder_level THEN 1 ELSE 0 END) as low_stock_count')
            ->first();
    }
}
