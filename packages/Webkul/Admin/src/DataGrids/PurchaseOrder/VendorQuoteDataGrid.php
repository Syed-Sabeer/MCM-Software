<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class VendorQuoteDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('vendor_quotes')
            ->leftJoin('organizations', 'vendor_quotes.organization_id', '=', 'organizations.id')
            ->leftJoin('job_orders', 'vendor_quotes.job_order_id', '=', 'job_orders.id')
            ->addSelect('vendor_quotes.id', 'vendor_quotes.vendor_quote_number', 'vendor_quotes.issue_date', 'vendor_quotes.status', 'organizations.id as organization_id', 'organizations.name as vendor_name', 'job_orders.id as job_order_id', 'job_orders.job_order_number');

        $this->addFilter('vendor_quote_number', 'vendor_quotes.vendor_quote_number');
        $this->addFilter('vendor_name', 'organizations.name');
        $this->addFilter('job_order_number', 'job_orders.job_order_number');
        $this->addFilter('issue_date', 'vendor_quotes.issue_date');
        $this->addFilter('status', 'vendor_quotes.status');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        foreach ([
            ['vendor_quote_number', 'Vendor Quote #', 'string'],
            ['vendor_name', 'Vendor', 'string'],
            ['job_order_number', 'Job Order', 'string'],
            ['issue_date', 'Issue Date', 'date'],
            ['status', 'Status', 'string'],
        ] as [$index, $label, $type]) {
            $closure = fn ($row) => $row->{$index} ?: '--';

            if ($index === 'vendor_quote_number') {
                $closure = fn ($row) => '<a href="'.e(route('admin.vendor_quotes.view', $row->id)).'" class="text-brandColor">'.e($row->vendor_quote_number).'</a>';
            }

            if ($index === 'vendor_name') {
                $closure = fn ($row) => $row->organization_id
                    ? '<a href="'.e(route('admin.contacts.organizations.view', $row->organization_id)).'" class="text-brandColor">'.e($row->vendor_name).'</a>'
                    : '--';
            }

            if ($index === 'job_order_number') {
                $closure = fn ($row) => $row->job_order_id
                    ? '<a href="'.e(route('admin.job_orders.view', $row->job_order_id)).'" class="text-brandColor">'.e($row->job_order_number).'</a>'
                    : '--';
            }

            $this->addColumn([
                'index' => $index,
                'label' => $label,
                'type' => $type,
                'sortable' => true,
                'filterable' => true,
                'closure' => $closure,
            ]);
        }
    }

    public function prepareActions(): void
    {
        $this->addAction(['index' => 'view', 'icon' => 'icon-eye', 'title' => 'View', 'method' => 'GET', 'url' => fn ($row) => route('admin.vendor_quotes.view', $row->id)]);
        $this->addAction(['index' => 'edit', 'icon' => 'icon-edit', 'title' => 'Edit', 'method' => 'GET', 'url' => fn ($row) => route('admin.vendor_quotes.edit', $row->id)]);
        if (bouncer()->hasPermission('vendor_quotes.print')) {
            $this->addAction(['index' => 'print', 'icon' => 'icon-print', 'title' => 'Print', 'method' => 'GET', 'url' => fn ($row) => route('admin.vendor_quotes.print', $row->id)]);
        }
        $this->addAction(['index' => 'create_po', 'icon' => 'icon-note', 'title' => 'Create Vendor PO', 'method' => 'GET', 'url' => fn ($row) => route('admin.purchase_orders.create', ['vendor_quote_id' => $row->id])]);
        $this->addAction(['index' => 'delete', 'icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'DELETE', 'url' => fn ($row) => route('admin.vendor_quotes.delete', $row->id)]);
    }

    public function prepareMassActions(): void
    {
        $this->addMassAction(['icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'POST', 'url' => route('admin.vendor_quotes.mass_delete')]);
    }
}
