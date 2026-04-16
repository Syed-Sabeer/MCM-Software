<?php

namespace Webkul\Admin\DataGrids\Settings;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class MaterialReferenceDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('material_references')
            ->leftJoin('material_reference_vendor', 'material_references.id', '=', 'material_reference_vendor.material_reference_id')
            ->leftJoin('organizations', 'material_reference_vendor.organization_id', '=', 'organizations.id')
            ->addSelect(
                'material_references.id',
                'material_references.name',
                'material_references.qty',
                'material_references.unit',
                'material_references.color_name',
                'material_references.color_code',
                'material_references.created_at',
                DB::raw("GROUP_CONCAT(DISTINCT organizations.name ORDER BY organizations.name SEPARATOR ', ') as vendor_names")
            )
            ->groupBy(
                'material_references.id',
                'material_references.name',
                'material_references.qty',
                'material_references.unit',
                'material_references.color_name',
                'material_references.color_code',
                'material_references.created_at'
            );

        $this->addFilter('id', 'material_references.id');
        $this->addFilter('name', 'material_references.name');
        $this->addFilter('unit', 'material_references.unit');
        $this->addFilter('created_at', 'material_references.created_at');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'ID',
            'type'       => 'integer',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => 'Material Name',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'qty',
            'label'      => 'Qty',
            'type'       => 'float',
            'sortable'   => true,
            'filterable' => false,
            'closure'    => fn ($row) => number_format((float) $row->qty, 4, '.', ''),
        ]);

        $this->addColumn([
            'index'      => 'unit',
            'label'      => 'Unit',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'vendor_names',
            'label'      => 'Vendors',
            'type'       => 'string',
            'sortable'   => false,
            'filterable' => false,
            'closure'    => fn ($row) => $row->vendor_names ?: '-',
        ]);

        $this->addColumn([
            'index'      => 'color_code',
            'label'      => 'Color',
            'type'       => 'string',
            'sortable'   => false,
            'filterable' => false,
            'closure'    => fn ($row) => $row->color_code
                ? '<div class="flex items-center gap-2"><span class="inline-block h-4 w-4 rounded-full border border-gray-300" style="background-color: '.e($row->color_code).'"></span><span>'.e($row->color_name ?: $row->color_code).'</span></div>'
                : '-',
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => 'Created At',
            'type'            => 'date',
            'sortable'        => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'closure'         => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    public function prepareActions(): void
    {
        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => 'Edit',
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.settings.material_references.edit', $row->id),
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => 'Delete',
            'method' => 'DELETE',
            'url'    => fn ($row) => route('admin.settings.material_references.delete', $row->id),
        ]);
    }

    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => 'Delete',
            'method' => 'POST',
            'url'    => route('admin.settings.material_references.mass_delete'),
        ]);
    }
}
