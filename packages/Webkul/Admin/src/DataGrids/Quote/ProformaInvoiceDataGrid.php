<?php

namespace Webkul\Admin\DataGrids\Quote;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class ProformaInvoiceDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('proforma_invoices')
            ->addSelect(
                'proforma_invoices.id',
                'proforma_invoices.proforma_number',
                'proforma_invoices.quote_id',
                'proforma_invoices.issue_date',
                'proforma_invoices.grand_total',
                'proforma_invoices.received_amount',
                'proforma_invoices.remaining_amount',
                'proforma_invoices.status',
                'organizations.id as organization_id',
                'organizations.name as organization_name',
                'quotes.quote_number'
            )
            ->leftJoin('organizations', 'proforma_invoices.organization_id', '=', 'organizations.id')
            ->leftJoin('quotes', 'proforma_invoices.quote_id', '=', 'quotes.id');

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('proforma_invoices.sales_owner_id', $userIds);
        }

        $this->addFilter('proforma_number', 'proforma_invoices.proforma_number');
        $this->addFilter('organization_name', 'organizations.name');
        $this->addFilter('quote_number', 'quotes.quote_number');
        $this->addFilter('issue_date', 'proforma_invoices.issue_date');
        $this->addFilter('status', 'proforma_invoices.status');
        $this->addFilter('grand_total', 'proforma_invoices.grand_total');
        $this->addFilter('received_amount', 'proforma_invoices.received_amount');
        $this->addFilter('remaining_amount', 'proforma_invoices.remaining_amount');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'proforma_number',
            'label'      => 'Proforma #',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'organization_name',
            'label'              => 'Customer',
            'type'               => 'string',
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => \Webkul\Contact\Repositories\OrganizationRepository::class,
                'column'     => [
                    'label' => 'name',
                    'value' => 'name',
                ],
            ],
            'closure'            => fn ($row) => $row->organization_name ?: '--',
        ]);

        $this->addColumn([
            'index'      => 'quote_number',
            'label'      => 'Quote',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => $row->quote_number ?: '--',
        ]);

        $this->addColumn([
            'index'      => 'issue_date',
            'label'      => 'Issue Date',
            'type'       => 'date',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->issue_date, 'd M Y'),
        ]);

        $this->addColumn([
            'index'      => 'grand_total',
            'label'      => 'Grand Total',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->grand_total, 2),
        ]);

        $this->addColumn([
            'index'      => 'received_amount',
            'label'      => 'Received',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->received_amount, 2),
        ]);

        $this->addColumn([
            'index'      => 'remaining_amount',
            'label'      => 'Remaining',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->remaining_amount, 2),
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('proforma_invoices.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => 'Edit',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.proforma_invoices.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('proforma_invoices.edit')) {
            $this->addAction([
                'index'  => 'view',
                'icon'   => 'icon-eye',
                'title'  => 'View',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.proforma_invoices.view', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('job_orders.create')) {
            $this->addAction([
                'index'  => 'create_job_order',
                'icon'   => 'icon-note',
                'title'  => 'Create Job Order',
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.job_orders.create', ['proforma_invoice_id' => $row->id]),
            ]);
        }

        if (bouncer()->hasPermission('proforma_invoices.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.proforma_invoices.delete', $row->id),
            ]);
        }
    }

    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('proforma_invoices.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => 'Delete',
                'method' => 'POST',
                'url'    => route('admin.proforma_invoices.mass_delete'),
            ]);
        }
    }
}
