<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class RequirementDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('job_order_requirements')
            ->leftJoin('job_orders', 'job_order_requirements.job_order_id', '=', 'job_orders.id')
            ->leftJoin('organizations', 'job_orders.organization_id', '=', 'organizations.id')
            ->addSelect(
                'job_order_requirements.id',
                'job_order_requirements.job_order_id',
                'job_order_requirements.material_name',
                'job_order_requirements.unit',
                'job_order_requirements.required_qty',
                'job_order_requirements.received_qty',
                'job_order_requirements.balance_qty',
                'job_order_requirements.status',
                'job_orders.job_order_number',
                'organizations.id as organization_id',
                'organizations.name as customer_name'
            );

        $this->addFilter('job_order_number', 'job_orders.job_order_number');
        $this->addFilter('customer_name', 'organizations.name');
        $this->addFilter('material_name', 'job_order_requirements.material_name');
        $this->addFilter('status', 'job_order_requirements.status');

        if (request()->filled('job_order_id')) {
            $queryBuilder->where('job_order_requirements.job_order_id', request('job_order_id'));
        }

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        $this->addColumn(['index' => 'job_order_number', 'label' => 'Job Order', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => '<a href="'.e(route('admin.job_orders.view', $row->job_order_id)).'" class="text-brandColor">'.e($row->job_order_number).'</a>']);
        $this->addColumn(['index' => 'customer_name', 'label' => 'Customer', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => $row->organization_id ? '<a href="'.e(route('admin.contacts.organizations.view', $row->organization_id)).'" class="text-brandColor">'.e($row->customer_name).'</a>' : '--']);
        $this->addColumn(['index' => 'material_name', 'label' => 'Material', 'type' => 'string', 'sortable' => true, 'filterable' => true]);
        $this->addColumn(['index' => 'required_qty', 'label' => 'Required Qty', 'type' => 'string', 'sortable' => true, 'filterable' => false, 'closure' => fn ($row) => rtrim(rtrim(number_format((float) $row->required_qty, 4, '.', ''), '0'), '.') . ' ' . ($row->unit ?: '')]);
        $this->addColumn(['index' => 'received_qty', 'label' => 'Received Qty', 'type' => 'string', 'sortable' => true, 'filterable' => false, 'closure' => fn ($row) => rtrim(rtrim(number_format((float) $row->received_qty, 4, '.', ''), '0'), '.')]);
        $this->addColumn(['index' => 'balance_qty', 'label' => 'Balance Qty', 'type' => 'string', 'sortable' => true, 'filterable' => false, 'closure' => fn ($row) => rtrim(rtrim(number_format((float) $row->balance_qty, 4, '.', ''), '0'), '.')]);
        $this->addColumn(['index' => 'status', 'label' => 'Status', 'type' => 'string', 'sortable' => true, 'filterable' => true]);
    }

    public function prepareActions(): void
    {
        $this->addAction(['index' => 'job_order', 'icon' => 'icon-eye', 'title' => 'View Job Order', 'method' => 'GET', 'url' => fn ($row) => route('admin.job_orders.view', $row->job_order_id)]);
        $this->addAction(['index' => 'vendor_quote', 'icon' => 'icon-note', 'title' => 'Create Vendor Quote', 'method' => 'GET', 'url' => fn ($row) => route('admin.vendor_quotes.create', ['job_order_id' => $row->job_order_id, 'requirement_ids' => [$row->id]])]);
        $this->addAction(['index' => 'vendor_po', 'icon' => 'icon-note', 'title' => 'Create Vendor PO', 'method' => 'GET', 'url' => fn ($row) => route('admin.purchase_orders.create', ['job_order_id' => $row->job_order_id, 'requirement_ids' => [$row->id]])]);

        if (bouncer()->hasPermission('requirements.delete')) {
            $this->addAction(['index' => 'delete', 'icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'DELETE', 'url' => fn ($row) => route('admin.requirements.delete', $row->id)]);
        }
    }

    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('requirements.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'POST',
                'url'    => route('admin.requirements.mass_delete'),
            ]);
        }
    }
}
