<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class PurchaseOrderDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('purchase_orders')
            ->addSelect(
                'purchase_orders.id',
                'purchase_orders.po_number',
                'purchase_orders.job_number',
                'purchase_orders.sub_total',
                'purchase_orders.tax_amount',
                'purchase_orders.freight',
                'purchase_orders.grand_total',
                'purchase_orders.created_at',
                'users.id as user_id',
                'users.name as sales_person',
                'persons.id as person_id',
                'persons.name as person_name'
            )
            ->leftJoin('users', 'purchase_orders.user_id', '=', 'users.id')
            ->leftJoin('persons', 'purchase_orders.person_id', '=', 'persons.id');

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('purchase_orders.user_id', $userIds);
        }

        $this->addFilter('id', 'purchase_orders.id');
        $this->addFilter('po_number', 'purchase_orders.po_number');
        $this->addFilter('job_number', 'purchase_orders.job_number');
        $this->addFilter('sales_person', 'users.name');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('created_at', 'purchase_orders.created_at');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'po_number',
            'label'      => trans('admin::app.purchase-orders.index.datagrid.po-number'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'job_number',
            'label'      => trans('admin::app.purchase-orders.index.datagrid.job-number'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'sales_person',
            'label'              => trans('admin::app.purchase-orders.index.datagrid.sales-person'),
            'type'               => 'string',
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => \Webkul\User\Repositories\UserRepository::class,
                'column'     => [
                    'label' => 'name',
                    'value' => 'name',
                ],
            ],
        ]);

        $this->addColumn([
            'index'              => 'person_name',
            'label'              => trans('admin::app.purchase-orders.index.datagrid.person'),
            'type'               => 'string',
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => \Webkul\Contact\Repositories\PersonRepository::class,
                'column'     => [
                    'label' => 'name',
                    'value' => 'name',
                ],
            ],
            'closure'    => function ($row) {
                if (! $row->person_id) {
                    return '--';
                }

                $route = route('admin.contacts.persons.view', $row->person_id);

                return "<a class=\"text-brandColor transition-all hover:underline\" href='" . $route . "'>" . $row->person_name . '</a>';
            },
        ]);

        $this->addColumn([
            'index'      => 'sub_total',
            'label'      => trans('admin::app.purchase-orders.index.datagrid.subtotal'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->sub_total, 2),
        ]);

        $this->addColumn([
            'index'      => 'tax_amount',
            'label'      => trans('admin::app.purchase-orders.index.datagrid.tax'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->tax_amount, 2),
        ]);

        $this->addColumn([
            'index'      => 'freight',
            'label'      => trans('admin::app.purchase-orders.index.datagrid.freight'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->freight, 2),
        ]);

        $this->addColumn([
            'index'      => 'grand_total',
            'label'      => trans('admin::app.purchase-orders.index.datagrid.grand-total'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->grand_total, 2),
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.purchase-orders.index.datagrid.created-at'),
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('purchase_orders.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.purchase-orders.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.purchase_orders.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('purchase_orders.print')) {
            $this->addAction([
                'index'  => 'print',
                'icon'   => 'icon-print',
                'title'  => trans('admin::app.purchase-orders.index.datagrid.print'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.purchase_orders.print', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('purchase_orders.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.purchase-orders.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.purchase_orders.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('purchase_orders.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.purchase-orders.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.purchase_orders.mass_delete'),
            ]);
        }
    }
}
