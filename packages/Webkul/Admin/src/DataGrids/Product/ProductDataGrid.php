<?php

namespace Webkul\Admin\DataGrids\Product;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class ProductDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('products')
            ->leftJoin('organizations', 'products.customer_organization_id', '=', 'organizations.id')
            ->select(
                'products.id',
                'products.cover_image',
                'products.name',
                'products.sku',
                'products.internal_code',
                'products.customer_organization_id',
                'organizations.name as customer_name',
                'products.created_at'
            );

        $this->addFilter('id', 'products.id');
        $this->addFilter('name', 'products.name');
        $this->addFilter('sku', 'products.sku');
        $this->addFilter('customer_organization_id', 'products.customer_organization_id');
        $this->addFilter('customer_name', 'organizations.name');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'cover_image',
            'label'      => 'Cover',
            'type'       => 'string',
            'sortable'   => false,
            'searchable' => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if (empty($row->cover_image)) {
                    return '<span class="text-xs text-gray-500">No image</span>';
                }

                $url = secure_url('public/storage/'.$row->cover_image);

                return '<img src="'.$url.'" alt="Cover" class="h-10 w-10 rounded object-cover border border-gray-200 dark:border-gray-700">';
            },
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => 'Product Name',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'sku',
            'label'      => 'Item Code',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'internal_code',
            'label'      => 'Internal Code',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => false,
            'closure'    => fn ($row) => $row->internal_code ?: '—',
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => 'Customer',
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => $row->customer_name ?: '—',
        ]);

        $this->addColumn([
            'index'      => 'product_type',
            'label'      => 'Product Type',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                $isCustomerSpecific = ! empty($row->customer_organization_id);

                if ($isCustomerSpecific) {
                    return '<span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Customer Specific</span>';
                }

                return '<span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">Global</span>';
            },
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('products.view')) {
            $this->addAction([
                'index'  => 'view',
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.products.index.datagrid.view'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.products.view', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('products.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.products.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.products.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('products.create')) {
            $this->addAction([
                'index'  => 'duplicate',
                'icon'   => 'icon-add',
                'title'  => trans('admin::app.products.index.datagrid.duplicate'),
                'method' => 'POST',
                'url'    => fn ($row) => route('admin.products.duplicate', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('products.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.products.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.products.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.products.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.products.mass_delete'),
        ]);
    }
}
