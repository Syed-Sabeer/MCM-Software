<?php

namespace Webkul\Admin\DataGrids\PurchaseOrder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Support\DocumentStatusOptions;
use Webkul\DataGrid\DataGrid;

class JobOrderDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('job_orders')
            ->leftJoin('organizations', 'job_orders.organization_id', '=', 'organizations.id')
            ->leftJoin('proforma_invoices', 'job_orders.proforma_invoice_id', '=', 'proforma_invoices.id')
            ->addSelect('job_orders.id', 'job_orders.job_order_number', 'job_orders.required_delivery_date', 'job_orders.status', 'organizations.id as organization_id', 'organizations.name as customer_name', 'proforma_invoices.id as proforma_invoice_id', 'proforma_invoices.proforma_number');

        if ($organizationId = request('organization_id')) {
            $queryBuilder->where('job_orders.organization_id', $organizationId);
        }

        $this->addFilter('organization_id', 'job_orders.organization_id');
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
            ['required_delivery_date', 'ETD', 'string'],
            ['status', 'Status', 'string'],
        ] as [$index, $label, $type]) {
            $closure = fn ($row) => $row->{$index} ?: '--';

            if ($index === 'job_order_number') {
                $closure = fn ($row) => '<a href="'.e(route('admin.job_orders.view', $row->id)).'" class="text-brandColor">'.e($row->job_order_number).'</a>';
            }

            if ($index === 'customer_name') {
                $closure = fn ($row) => $row->organization_id
                    ? '<a href="'.e(route('admin.contacts.organizations.view', $row->organization_id)).'" class="text-brandColor">'.e($row->customer_name).'</a>'
                    : '--';
            }

            if ($index === 'proforma_number') {
                $closure = fn ($row) => $row->proforma_invoice_id
                    ? '<a href="'.e(route('admin.proforma_invoices.view', $row->proforma_invoice_id)).'" class="text-brandColor">'.e($row->proforma_number).'</a>'
                    : '--';
            }

            if ($index === 'required_delivery_date') {
                $closure = fn ($row) => $this->formatDate($row->required_delivery_date);
            }

            if ($index === 'status') {
                $closure = fn ($row) => e(DocumentStatusOptions::label('job_order', $row->status));
            }

            $column = [
                'index' => $index,
                'label' => $label,
                'type' => $type,
                'sortable' => true,
                'filterable' => true,
                'closure' => $closure,
            ];

            if ($index === 'status') {
                $column['filterable_type'] = 'dropdown';
                $column['filterable_options'] = DocumentStatusOptions::filterOptions('job_order');
            }

            $this->addColumn($column);
        }
    }

    public function prepareActions(): void
    {
        $this->addAction(['index' => 'view', 'icon' => 'icon-eye', 'title' => 'View', 'method' => 'GET', 'url' => fn ($row) => route('admin.job_orders.view', $row->id)]);
        $this->addAction(['index' => 'edit', 'icon' => 'icon-edit', 'title' => 'Edit', 'method' => 'GET', 'url' => fn ($row) => route('admin.job_orders.edit', $row->id)]);
        $this->addAction(['index' => 'delete', 'icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'DELETE', 'url' => fn ($row) => route('admin.job_orders.delete', $row->id)]);
    }

    public function prepareMassActions(): void
    {
        $this->addMassAction(['icon' => 'icon-delete', 'title' => 'Delete', 'method' => 'POST', 'url' => route('admin.job_orders.mass_delete')]);
    }

    protected function formatDate($value): string
    {
        if (empty($value) || (is_string($value) && str_starts_with($value, '0000-00-00'))) {
            return '-';
        }

        try {
            $date = \Illuminate\Support\Carbon::parse($value);

            return (int) $date->format('Y') <= 1 ? '-' : $date->format('Y-m-d');
        } catch (\Throwable) {
            return '-';
        }
    }
}
