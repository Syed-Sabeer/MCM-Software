<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class JobOrderDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('job_orders')
            ->leftJoin('organizations', 'job_orders.organization_id', '=', 'organizations.id')
            ->leftJoin('proforma_invoices', 'job_orders.proforma_invoice_id', '=', 'proforma_invoices.id')
            ->addSelect('job_orders.id', 'job_orders.job_order_number', 'job_orders.required_delivery_date', 'job_orders.status', 'organizations.name as customer_name', 'proforma_invoices.proforma_number');

        $this->addFilter('job_order_number', 'job_orders.job_order_number');
        $this->addFilter('customer_name', 'organizations.name');
        $this->addFilter('proforma_number', 'proforma_invoices.proforma_number');
        $this->addFilter('required_delivery_date', 'job_orders.required_delivery_date');
        $this->addFilter('status', 'job_orders.status');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        foreach ([
            ['job_order_number', 'Job Order #', 'string'],
            ['customer_name', 'Customer', 'string'],
            ['proforma_number', 'Proforma', 'string'],
            ['required_delivery_date', 'Required Delivery', 'date'],
            ['status', 'Status', 'string'],
        ] as [$index, $label, $type]) {
            $this->addColumn([
                'index' => $index,
                'label' => $label,
                'type' => $type,
                'sortable' => true,
                'filterable' => true,
                'closure' => fn ($row) => $row->{$index} ?: '--',
            ]);
        }
    }

    public function prepareActions(): void
    {
        $this->addAction(['index' => 'view', 'icon' => 'icon-view', 'title' => 'View', 'method' => 'GET', 'url' => fn ($row) => route('admin.job_orders.view', $row->id)]);
        $this->addAction(['index' => 'edit', 'icon' => 'icon-edit', 'title' => 'Edit', 'method' => 'GET', 'url' => fn ($row) => route('admin.job_orders.edit', $row->id)]);
        $this->addAction(['index' => 'delete', 'icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'DELETE', 'url' => fn ($row) => route('admin.job_orders.delete', $row->id)]);
    }

    public function prepareMassActions(): void
    {
        $this->addMassAction(['icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'POST', 'url' => route('admin.job_orders.mass_delete')]);
    }
}
