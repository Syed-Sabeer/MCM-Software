<?php

namespace Webkul\Admin\DataGrids\Quote;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class InvoiceDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $query = DB::table('invoices')
            ->leftJoin('organizations', 'invoices.organization_id', '=', 'organizations.id')
            ->leftJoin('proforma_invoices', 'invoices.proforma_invoice_id', '=', 'proforma_invoices.id')
            ->select('invoices.*', 'organizations.name as organization_name', 'proforma_invoices.proforma_number');

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $query->whereIn('invoices.sales_owner_id', $userIds);
        }
        if ($organizationId = request('organization_id')) {
            $query->where('invoices.organization_id', $organizationId);
        }

        foreach (['invoice_number', 'issue_date', 'status', 'grand_total', 'advance_applied', 'remaining_amount'] as $column) {
            $this->addFilter($column, 'invoices.'.$column);
        }
        $this->addFilter('organization_name', 'organizations.name');
        $this->addFilter('proforma_number', 'proforma_invoices.proforma_number');

        return $query;
    }

    public function prepareColumns(): void
    {
        $this->addColumn(['index' => 'invoice_number', 'label' => 'Invoice #', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => '<a class="text-brandColor" href="'.e(route('admin.invoices.view', $row->id)).'">'.e($row->invoice_number).'</a>']);
        $this->addColumn(['index' => 'organization_name', 'label' => 'Customer', 'type' => 'string', 'sortable' => true, 'filterable' => true]);
        $this->addColumn(['index' => 'proforma_number', 'label' => 'Proforma', 'type' => 'string', 'sortable' => true, 'filterable' => true]);
        $this->addColumn(['index' => 'issue_date', 'label' => 'Issue Date', 'type' => 'date', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => core()->formatDate($row->issue_date, 'd M Y')]);
        $this->addColumn(['index' => 'grand_total', 'label' => 'Total', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => core()->formatBasePrice($row->grand_total, 2)]);
        $this->addColumn(['index' => 'advance_applied', 'label' => 'Advance', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => core()->formatBasePrice($row->advance_applied, 2)]);
        $this->addColumn(['index' => 'remaining_amount', 'label' => 'Balance', 'type' => 'string', 'sortable' => true, 'filterable' => true, 'closure' => fn ($row) => core()->formatBasePrice($row->remaining_amount, 2)]);
        $this->addColumn(['index' => 'status', 'label' => 'Status', 'type' => 'string', 'sortable' => true, 'filterable' => true]);
    }

    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('invoices.view')) {
            $this->addAction(['index' => 'view', 'icon' => 'icon-eye', 'title' => 'View', 'method' => 'GET', 'url' => fn ($row) => route('admin.invoices.view', $row->id)]);
        }
    }
}
