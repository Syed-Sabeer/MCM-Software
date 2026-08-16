<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class MaterialInventoryDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('material_references')
            ->leftJoin('material_inventories', 'material_references.id', '=', 'material_inventories.material_reference_id')
            ->select([
                'material_references.id',
                'material_references.name',
                'material_references.unit',
                DB::raw('COALESCE(material_inventories.on_hand, 0) as on_hand'),
                DB::raw('COALESCE(material_inventories.average_unit_cost, 0) as average_unit_cost'),
                DB::raw('COALESCE(material_inventories.reorder_level, 0) as reorder_level'),
                DB::raw('(COALESCE(material_inventories.on_hand, 0) * COALESCE(material_inventories.average_unit_cost, 0)) as stock_value'),
            ]);

        $this->addFilter('id', 'material_references.id');
        $this->addFilter('name', 'material_references.name');
        $this->addFilter('unit', 'material_references.unit');
        $this->addFilter('on_hand', 'material_inventories.on_hand');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index' => 'name',
            'label' => 'Material',
            'type' => 'string',
            'sortable' => true,
            'filterable' => true,
            'closure' => fn ($row) => '<a class="font-medium text-brandColor" href="'.e(route('admin.inventory.view', $row->id)).'">'.e($row->name).'</a>',
        ]);
        $this->addColumn(['index' => 'unit', 'label' => 'Unit', 'type' => 'string', 'sortable' => true, 'filterable' => true]);
        $this->addColumn([
            'index' => 'on_hand',
            'label' => 'On Hand',
            'type' => 'float',
            'sortable' => true,
            'filterable' => true,
            'closure' => fn ($row) => $this->quantity($row->on_hand),
        ]);
        $this->addColumn([
            'index' => 'average_unit_cost',
            'label' => 'Average Cost',
            'type' => 'string',
            'sortable' => true,
            'filterable' => false,
            'closure' => fn ($row) => core()->formatBasePrice($row->average_unit_cost, 2),
        ]);
        $this->addColumn([
            'index' => 'stock_value',
            'label' => 'Stock Value',
            'type' => 'string',
            'sortable' => true,
            'filterable' => false,
            'closure' => fn ($row) => core()->formatBasePrice($row->stock_value, 2),
        ]);
        $this->addColumn([
            'index' => 'reorder_level',
            'label' => 'Reorder Level',
            'type' => 'float',
            'sortable' => true,
            'filterable' => false,
            'closure' => fn ($row) => $this->quantity($row->reorder_level),
        ]);
        $this->addColumn([
            'index' => 'stock_status',
            'label' => 'Status',
            'type' => 'string',
            'sortable' => false,
            'filterable' => false,
            'closure' => function ($row) {
                $onHand = (float) $row->on_hand;
                $reorder = (float) $row->reorder_level;

                if ($onHand <= 0) {
                    return '<span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">Out of Stock</span>';
                }

                if ($reorder > 0 && $onHand <= $reorder) {
                    return '<span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-700">Low Stock</span>';
                }

                return '<span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">In Stock</span>';
            },
        ]);
    }

    public function prepareActions(): void
    {
        $this->addAction([
            'index' => 'edit_stock',
            'icon' => 'icon-edit',
            'title' => 'Edit quantity and price',
            'method' => 'modal',
            'url' => fn ($row) => route('admin.inventory.edit', $row->id),
        ]);
        $this->addAction([
            'index' => 'view',
            'icon' => 'icon-eye',
            'title' => 'View movements',
            'method' => 'GET',
            'url' => fn ($row) => route('admin.inventory.view', $row->id),
        ]);
    }

    protected function quantity($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 4, '.', ','), '0'), '.');
    }
}
