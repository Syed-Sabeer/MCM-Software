<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class PurchaseOrderDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('purchase_orders')
            ->leftJoin('users', 'purchase_orders.user_id', '=', 'users.id')
            ->leftJoin('organizations', 'purchase_orders.organization_id', '=', 'organizations.id')
            ->leftJoin('job_orders', 'purchase_orders.job_order_id', '=', 'job_orders.id')
            ->addSelect(
                'purchase_orders.id',
                'purchase_orders.po_number',
                'purchase_orders.expected_receive_date',
                'purchase_orders.grand_total',
                'purchase_orders.status',
                'purchase_orders.created_at',
                'users.name as sales_person',
                'organizations.id as organization_id',
                'organizations.name as vendor_name',
                'job_orders.id as job_order_id',
                'job_orders.job_order_number'
            );

        if ($organizationId = request('organization_id')) {
            $queryBuilder->where('purchase_orders.organization_id', $organizationId);
        }

        $this->addFilter('organization_id', 'purchase_orders.organization_id');
        $this->addFilter('po_number', 'purchase_orders.po_number');
        $this->addFilter('vendor_name', 'organizations.name');
        $this->addFilter('job_order_number', 'job_orders.job_order_number');
        $this->addFilter('expected_receive_date', 'purchase_orders.expected_receive_date');
        $this->addFilter('status', 'purchase_orders.status');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        $this->addColumn(['index' => 'po_number', 'label' => 'PO #', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => '<a href="'.e(route('admin.purchase_orders.view', $row->id)).'" class="text-brandColor">'.e($row->po_number).'</a>']);
        $this->addColumn(['index' => 'vendor_name', 'label' => 'Vendor', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => $row->organization_id ? '<a href="'.e(route('admin.contacts.organizations.view', $row->organization_id)).'" class="text-brandColor">'.e($row->vendor_name).'</a>' : '--']);
        $this->addColumn(['index' => 'job_order_number', 'label' => 'Job Order', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => $row->job_order_id ? '<a href="'.e(route('admin.job_orders.view', $row->job_order_id)).'" class="text-brandColor">'.e($row->job_order_number).'</a>' : '--']);
        $this->addColumn(['index' => 'expected_receive_date', 'label' => 'Expected Receive Date', 'type' => 'date', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => $row->expected_receive_date ? core()->formatDate($row->expected_receive_date, 'd M Y') : '--']);
        $this->addColumn(['index' => 'grand_total', 'label' => 'Grand Total', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => core()->formatBasePrice($row->grand_total, 2)]);
        $this->addColumn(['index' => 'status', 'label' => 'Status', 'type' => 'string', 'sortable' => true, 'filterable' => true]);
    }

    public function prepareActions(): void
    {
        $this->addAction(['index' => 'view', 'icon' => 'icon-eye', 'title' => 'View', 'method' => 'GET', 'url' => fn ($row) => route('admin.purchase_orders.view', $row->id)]);
        $this->addAction(['index' => 'edit', 'icon' => 'icon-edit', 'title' => 'Edit', 'method' => 'GET', 'url' => fn ($row) => route('admin.purchase_orders.edit', $row->id)]);
        $this->addAction(['index' => 'receipt', 'icon' => 'icon-note', 'title' => 'Receive Goods', 'method' => 'GET', 'url' => fn ($row) => route('admin.goods_receipts.create', ['purchase_order_id' => $row->id])]);
        $this->addAction(['index' => 'print', 'icon' => 'icon-print', 'title' => 'Print', 'method' => 'GET', 'url' => fn ($row) => route('admin.purchase_orders.print', $row->id)]);
        $this->addAction(['index' => 'delete', 'icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'DELETE', 'url' => fn ($row) => route('admin.purchase_orders.delete', $row->id)]);
    }

    public function prepareMassActions(): void
    {
        $this->addMassAction(['icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'POST', 'url' => route('admin.purchase_orders.mass_delete')]);
    }
}
