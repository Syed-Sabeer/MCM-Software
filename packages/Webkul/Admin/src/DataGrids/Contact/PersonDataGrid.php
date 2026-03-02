<?php

namespace Webkul\Admin\DataGrids\Contact;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Contact\Repositories\OrganizationRepository;
use Webkul\DataGrid\DataGrid;

class PersonDataGrid extends DataGrid
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(protected OrganizationRepository $organizationRepository) {}

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('persons')
            ->addSelect(
                'persons.id',
                DB::raw("NULLIF(TRIM(CONCAT_WS(' ', persons.first_name, persons.last_name)), '') as person_name"),
                'persons.name as legacy_name',
                'persons.type',
                'persons.emails',
                'persons.contact_numbers',
                'persons.email',
                'persons.email_secondary',
                'persons.phone',
                'persons.cell_phone',
                'persons.direct_phone',
                'organizations.name as organization',
                'organizations.id as organization_id'
            )
            ->leftJoin('organizations', 'persons.organization_id', '=', 'organizations.id');

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('persons.user_id', $userIds);
        }

        if ($organizationId = request('organization_id')) {
            $queryBuilder->where('persons.organization_id', $organizationId);
        }

        $this->addFilter('id', 'persons.id');
        $this->addFilter('person_name', DB::raw("COALESCE(NULLIF(TRIM(CONCAT_WS(' ', persons.first_name, persons.last_name)), ''), persons.name)"));
        $this->addFilter('type', 'persons.type');
        $this->addFilter('organization', 'organizations.name');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.contacts.persons.index.datagrid.id'),
            'type'       => 'integer',
            'filterable' => true,
            'sortable'   => true,
            'searchable' => true,
        ]);

        $this->addColumn([
            'index'      => 'person_name',
            'label'      => trans('admin::app.contacts.persons.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'searchable' => true,
            'closure'    => function ($row) {
                if (! empty($row->person_name)) {
                    return $row->person_name;
                }

                return $row->legacy_name ?? '';
            },
        ]);

        $this->addColumn([
            'index'              => 'type',
            'label'              => trans('admin::app.contacts.persons.index.datagrid.type'),
            'type'               => 'string',
            'sortable'           => true,
            'filterable'         => true,
            'searchable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                ['label' => trans('admin::app.contacts.persons.index.datagrid.types.customer'), 'value' => 'customer'],
                ['label' => trans('admin::app.contacts.persons.index.datagrid.types.vendor'), 'value' => 'vendor'],
                ['label' => trans('admin::app.contacts.persons.index.datagrid.types.employee'), 'value' => 'employee'],
                ['label' => trans('admin::app.contacts.persons.index.datagrid.types.partner'), 'value' => 'partner'],
                ['label' => trans('admin::app.contacts.persons.index.datagrid.types.other'), 'value' => 'other'],
            ],
            'closure'    => function ($row) {
                $types = [
                    'customer' => '<span class="badge badge-round badge-success"></span> ' . trans('admin::app.contacts.persons.index.datagrid.types.customer'),
                    'vendor'   => '<span class="badge badge-round badge-warning"></span> ' . trans('admin::app.contacts.persons.index.datagrid.types.vendor'),
                    'employee' => '<span class="badge badge-round badge-info"></span> ' . trans('admin::app.contacts.persons.index.datagrid.types.employee'),
                    'partner'  => '<span class="badge badge-round badge-primary"></span> ' . trans('admin::app.contacts.persons.index.datagrid.types.partner'),
                    'other'    => '<span class="badge badge-round badge-secondary"></span> ' . trans('admin::app.contacts.persons.index.datagrid.types.other'),
                ];

                return $types[$row->type] ?? ucfirst($row->type ?? 'customer');
            },
        ]);

        $this->addColumn([
            'index'      => 'emails',
            'label'      => trans('admin::app.contacts.persons.index.datagrid.emails'),
            'type'       => 'string',
            'sortable'   => false,
            'filterable' => true,
            'searchable' => true,
            'closure'    => function ($row) {
                if (! empty($row->email_secondary)) {
                    return $row->email_secondary;
                }

                if (! empty($row->email)) {
                    return $row->email;
                }

                return collect(json_decode($row->emails, true) ?? [])->pluck('value')->first() ?? '';
            },
        ]);

        $this->addColumn([
            'index'      => 'contact_numbers',
            'label'      => trans('admin::app.contacts.persons.index.datagrid.contact-numbers'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'searchable' => true,
            'closure'    => function ($row) {
                if (! empty($row->cell_phone)) {
                    return $row->cell_phone;
                }

                return collect(json_decode($row->contact_numbers, true) ?? [])->pluck('value')->first() ?? '';
            },
        ]);

        $this->addColumn([
            'index'              => 'organization',
            'label'              => trans('admin::app.contacts.persons.index.datagrid.organization-name'),
            'type'               => 'string',
            'searchable'         => true,
            'filterable'         => true,
            'sortable'           => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => OrganizationRepository::class,
                'column'     => [
                    'label' => 'name',
                    'value' => 'name',
                ],
            ],
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('contacts.persons.view')) {
            $this->addAction([
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.view'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.contacts.persons.view', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('contacts.persons.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.contacts.persons.edit', $row->id);
                },
            ]);
        }

        if (bouncer()->hasPermission('contacts.persons.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.contacts.persons.delete', $row->id);
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('contacts.persons.delete')) {
            $this->addMassAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.contacts.persons.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.contacts.persons.mass_delete'),
            ]);
        }
    }
}
