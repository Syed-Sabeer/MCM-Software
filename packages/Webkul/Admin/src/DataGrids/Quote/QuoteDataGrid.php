<?php

namespace Webkul\Admin\DataGrids\Quote;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class QuoteDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('quotes')
            ->addSelect(
                'quotes.id',
                'quotes.quote_number',
                'quotes.subject',
                'quotes.status',
                'quotes.quote_date',
                'quotes.expired_at',
                'quotes.sub_total',
                'quotes.discount_amount',
                'quotes.tax_amount',
                'quotes.adjustment_amount',
                'quotes.grand_total',
                'quotes.created_at',
                'users.id as user_id',
                'users.name as sales_person',
                'persons.id as person_id',
                'persons.name as person_name',
                'organizations.id as organization_id',
                'organizations.name as organization_name'
            )
            ->leftJoin('users', 'quotes.user_id', '=', 'users.id')
            ->leftJoin('persons', 'quotes.person_id', '=', 'persons.id')
            ->leftJoin('organizations', 'quotes.organization_id', '=', 'organizations.id');

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('quotes.user_id', $userIds);
        }

        $this->addFilter('id', 'quotes.id');
        $this->addFilter('quote_number', 'quotes.quote_number');
        $this->addFilter('subject', 'quotes.subject');
        $this->addFilter('status', 'quotes.status');
        $this->addFilter('user', 'quotes.user_id');
        $this->addFilter('sales_person', 'users.name');
        $this->addFilter('person_name', 'persons.name');
        $this->addFilter('organization_name', 'organizations.name');
        $this->addFilter('quote_date', 'quotes.quote_date');
        $this->addFilter('expired_at', 'quotes.expired_at');
        $this->addFilter('created_at', 'quotes.created_at');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'quote_number',
            'label'      => 'Quote #',
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'subject',
            'label'      => trans('admin::app.quotes.index.datagrid.subject'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
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
            'index'              => 'sales_person',
            'label'              => trans('admin::app.quotes.index.datagrid.sales-person'),
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
            'label'              => trans('admin::app.quotes.index.datagrid.person'),
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

                return "<a class=\"text-brandColor transition-all hover:underline\" href='".$route."'>".$row->person_name.'</a>';
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'sub_total',
            'label'      => trans('admin::app.quotes.index.datagrid.subtotal'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->sub_total, 2),
        ]);

        $this->addColumn([
            'index'      => 'discount_amount',
            'label'      => trans('admin::app.quotes.index.datagrid.discount'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->discount_amount, 2),
        ]);

        $this->addColumn([
            'index'      => 'tax_amount',
            'label'      => trans('admin::app.quotes.index.datagrid.tax'),
            'type'       => 'string',
            'filterable' => true,
            'sortable'   => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->tax_amount, 2),
        ]);

        $this->addColumn([
            'index'      => 'adjustment_amount',
            'label'      => trans('admin::app.quotes.index.datagrid.adjustment'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => false,
            'closure'    => fn ($row) => core()->formatBasePrice($row->adjustment_amount, 2),
        ]);

        $this->addColumn([
            'index'      => 'grand_total',
            'label'      => trans('admin::app.quotes.index.datagrid.grand-total'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatBasePrice($row->grand_total, 2),
        ]);

        $this->addColumn([
            'index'      => 'quote_date',
            'label'      => 'Quote Date',
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => $row->quote_date ? core()->formatDate($row->quote_date, 'd M Y') : '--',
        ]);

        $this->addColumn([
            'index'      => 'expired_at',
            'label'      => trans('admin::app.quotes.index.datagrid.expired-at'),
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->expired_at, 'd M Y'),
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.quotes.index.datagrid.created-at'),
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
        if (bouncer()->hasPermission('quotes.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.quotes.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.quotes.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('quotes.edit')) {
            $this->addAction([
                'index'  => 'convert',
                'icon'   => 'icon-add',
                'title'  => 'Convert to Proforma',
                'method' => 'POST',
                'url'    => fn ($row) => route('admin.quotes.convert_to_proforma', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('quotes.print')) {
            $this->addAction([
                'index'  => 'print',
                'icon'   => 'icon-print',
                'title'  => trans('admin::app.quotes.index.datagrid.print'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.quotes.print', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('quotes.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.quotes.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.quotes.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('quotes.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.quotes.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.quotes.mass_delete'),
            ]);
        }
    }
}
