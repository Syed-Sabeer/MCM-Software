<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class VendorPayableDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('vendor_payables')
            ->leftJoin('organizations', 'vendor_payables.organization_id', '=', 'organizations.id')
            ->leftJoin('purchase_orders', 'vendor_payables.purchase_order_id', '=', 'purchase_orders.id')
            ->leftJoin('goods_receipts', 'vendor_payables.goods_receipt_id', '=', 'goods_receipts.id')
            ->addSelect(
                'vendor_payables.id',
                'vendor_payables.organization_id',
                'vendor_payables.purchase_order_id',
                'vendor_payables.goods_receipt_id',
                'vendor_payables.payable_number',
                'vendor_payables.payable_date',
                'vendor_payables.total_amount',
                'vendor_payables.paid_amount',
                'vendor_payables.remaining_amount',
                'vendor_payables.status',
                'organizations.name as vendor_name',
                'purchase_orders.po_number',
                'goods_receipts.goods_receipt_number'
            );

        $this->addFilter('payable_number', 'vendor_payables.payable_number');
        $this->addFilter('vendor_name', 'organizations.name');
        $this->addFilter('po_number', 'purchase_orders.po_number');
        $this->addFilter('goods_receipt_number', 'goods_receipts.goods_receipt_number');
        $this->addFilter('payable_date', 'vendor_payables.payable_date');
        $this->addFilter('status', 'vendor_payables.status');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'payable_number',
            'label'      => 'Payable #',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'vendor_name',
            'label'      => 'Vendor',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => $row->organization_id
                ? '<a href="'.e(route('admin.contacts.organizations.view', $row->organization_id)).'" class="text-brandColor">'.e($row->vendor_name).'</a>'
                : '--',
        ]);

        $this->addColumn([
            'index'      => 'po_number',
            'label'      => 'Purchase Order',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => $row->purchase_order_id
                ? '<a href="'.e(route('admin.purchase_orders.view', $row->purchase_order_id)).'" class="text-brandColor">'.e($row->po_number).'</a>'
                : '--',
        ]);

        $this->addColumn([
            'index'      => 'goods_receipt_number',
            'label'      => 'Goods Receipt',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => $row->goods_receipt_id
                ? '<a href="'.e(route('admin.goods_receipts.view', $row->goods_receipt_id)).'" class="text-brandColor">'.e($row->goods_receipt_number).'</a>'
                : '--',
        ]);

        $this->addColumn([
            'index'      => 'payable_date',
            'label'      => 'Payable Date',
            'type'       => 'date',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->payable_date, 'd M Y'),
        ]);

        $this->addColumn([
            'index'      => 'total_amount',
            'label'      => 'Total',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => false,
            'closure'    => fn ($row) => core()->formatBasePrice($row->total_amount, 2),
        ]);

        $this->addColumn([
            'index'      => 'remaining_amount',
            'label'      => 'Remaining',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => false,
            'closure'    => fn ($row) => core()->formatBasePrice($row->remaining_amount, 2),
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => ucfirst((string) $row->status),
        ]);
    }
}
