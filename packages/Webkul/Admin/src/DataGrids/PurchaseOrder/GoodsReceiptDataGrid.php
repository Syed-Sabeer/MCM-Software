<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class GoodsReceiptDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('goods_receipts')
            ->leftJoin('organizations', 'goods_receipts.vendor_id', '=', 'organizations.id')
            ->leftJoin('purchase_orders', 'goods_receipts.purchase_order_id', '=', 'purchase_orders.id')
            ->leftJoin('goods_receipt_items', 'goods_receipts.id', '=', 'goods_receipt_items.goods_receipt_id')
            ->groupBy('goods_receipts.id', 'organizations.name', 'purchase_orders.po_number')
            ->addSelect('goods_receipts.id', 'goods_receipts.goods_receipt_number', 'goods_receipts.receipt_date', 'organizations.name as vendor_name', 'purchase_orders.po_number', DB::raw('COALESCE(SUM(goods_receipt_items.line_total), 0) as total_received_value'));

        $this->addFilter('goods_receipt_number', 'goods_receipts.goods_receipt_number');
        $this->addFilter('vendor_name', 'organizations.name');
        $this->addFilter('po_number', 'purchase_orders.po_number');
        $this->addFilter('receipt_date', 'goods_receipts.receipt_date');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        $this->addColumn(['index' => 'goods_receipt_number', 'label' => 'Receipt #', 'type' => 'string', 'sortable' => true, 'filterable' => true]);
        $this->addColumn(['index' => 'vendor_name', 'label' => 'Vendor', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => $row->vendor_name ?: '--']);
        $this->addColumn(['index' => 'po_number', 'label' => 'Purchase Order', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => $row->po_number ?: '--']);
        $this->addColumn(['index' => 'receipt_date', 'label' => 'Receipt Date', 'type' => 'date', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => core()->formatDate($row->receipt_date, 'd M Y')]);
        $this->addColumn(['index' => 'total_received_value', 'label' => 'Received Value', 'type' => 'string', 'sortable' => true, 'filterable' => false, 'closure' => fn ($row) => core()->formatBasePrice($row->total_received_value, 2)]);
    }

    public function prepareActions(): void
    {
        $this->addAction(['index' => 'view', 'icon' => 'icon-eye', 'title' => 'View', 'method' => 'GET', 'url' => fn ($row) => route('admin.goods_receipts.view', $row->id)]);
        $this->addAction(['index' => 'edit', 'icon' => 'icon-edit', 'title' => 'Edit', 'method' => 'GET', 'url' => fn ($row) => route('admin.goods_receipts.edit', $row->id)]);
        $this->addAction(['index' => 'delete', 'icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'DELETE', 'url' => fn ($row) => route('admin.goods_receipts.delete', $row->id)]);
    }
}
