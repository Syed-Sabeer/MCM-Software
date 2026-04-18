<?php

namespace Webkul\Admin\DataGrids\Contact;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\DataGrid\DataGrid;

class OrganizationDataGrid extends DataGrid
{
    /**
     * Resolve route prefix from current route.
     */
    protected function getRoutePrefix(): string
    {
        $routeName = request()->route()?->getName() ?? '';

        if (str_contains($routeName, 'admin.customers.')) {
            return 'customers';
        }

        if (str_contains($routeName, 'admin.vendors.')) {
            return 'vendors';
        }

        return 'contacts';
    }

    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct(protected PersonRepository $personRepository) {}

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $routePrefix = $this->getRoutePrefix();

        $queryBuilder = DB::table('organizations')
            ->addSelect(
                'organizations.id',
                'organizations.name',
                'organizations.type',
                'organizations.address',
                'organizations.created_at'
            );

        // Hide the configured software company from customer/vendor organization listing pages.
        if (in_array($routePrefix, ['customers', 'vendors'], true)) {
            $softwareCompanyName = trim((string) core()->getConfigData('general.general.company_info.company_name'));

            if ($softwareCompanyName !== '') {
                $queryBuilder->whereRaw('LOWER(organizations.name) != ?', [mb_strtolower($softwareCompanyName)]);
            }
        }

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $queryBuilder->whereIn('organizations.user_id', $userIds);
        }

        // Apply type filter if type is set in query parameter
        if ($type = request('type')) {
            $type = strtolower($type);
            if (in_array($type, ['customer', 'vendor'])) {
                $queryBuilder->where(function ($query) use ($type) {
                    $query->whereRaw('LOWER(organizations.type) = ?', [$type])
                        ->orWhereNull('organizations.type')
                        ->orWhere('organizations.type', '');
                });
            }
        }

        $this->addFilter('id', 'organizations.id');
        $this->addFilter('organization', 'organizations.name');
        $this->addFilter('type', 'organizations.type');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $routePrefix = $this->getRoutePrefix();

        if (! in_array($routePrefix, ['customers', 'vendors'], true)) {
            $this->addColumn([
                'index'      => 'id',
                'label'      => trans('admin::app.contacts.organizations.index.datagrid.id'),
                'type'       => 'integer',
                'filterable' => true,
                'sortable'   => true,
            ]);
        }

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.contacts.organizations.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) use ($routePrefix) {
                $name = e($row->name ?? '');

                if (! bouncer()->hasPermission('contacts.organizations.view')) {
                    return $name;
                }

                $url = route("admin.{$routePrefix}.organizations.view", $row->id);

                return "<a href=\"{$url}\" class=\"text-brandColor hover:underline\">{$name}</a>";
            },
        ]);

        $this->addColumn([
            'index'      => 'persons_count',
            'label'      => trans('admin::app.contacts.organizations.index.datagrid.persons-count'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                $personsCount = $this->personRepository->findWhere(['organization_id' => $row->id])->count();

                return $personsCount;
            },
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('admin::app.settings.tags.index.datagrid.created-at'),
            'type'            => 'date',
            'searchable'      => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'sortable'        => true,
            'closure'         => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        $routePrefix = $this->getRoutePrefix();

        if (bouncer()->hasPermission('contacts.organizations.view')) {
            $this->addAction([
                'index'  => 'view',
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.contacts.organizations.index.datagrid.view'),
                'method' => 'GET',
                'url'    => fn ($row) => route("admin.{$routePrefix}.organizations.view", $row->id),
            ]);
        }

        if (bouncer()->hasPermission('contacts.organizations.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.contacts.organizations.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route("admin.{$routePrefix}.organizations.edit", $row->id),
            ]);
        }

        if (bouncer()->hasPermission('contacts.organizations.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.contacts.organizations.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route("admin.{$routePrefix}.organizations.delete", $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $routePrefix = $this->getRoutePrefix();

        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.contacts.organizations.index.datagrid.delete'),
            'method' => 'PUT',
            'url'    => route("admin.{$routePrefix}.organizations.mass_delete"),
        ]);
    }
}
