<?php

namespace Webkul\Admin\DataGrids\Activity;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Traits\ProvideDropdownOptions;
use Webkul\DataGrid\DataGrid;
use Webkul\Lead\Repositories\LeadRepository;
use Webkul\User\Repositories\UserRepository;

class ActivityDataGrid extends DataGrid
{
    use ProvideDropdownOptions;

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $queryBuilder = DB::table('activities')
            ->distinct()
            ->select(
                'activities.*',
                'activity_files.id as file_id',
                'activity_files.name as file_name',
                'activity_files.path as file_path',
                'leads.id as lead_id',
                'leads.title as lead_title',
                'leads.lead_pipeline_id',
                'users.id as created_by_id',
                'users.name as created_by',
                DB::raw("COALESCE(NULLIF(TRIM(persons.name), ''), NULLIF(TRIM(CONCAT_WS(' ', persons.first_name, persons.last_name)), ''), NULLIF(TRIM(entity_persons.name), ''), NULLIF(TRIM(CONCAT_WS(' ', entity_persons.first_name, entity_persons.last_name)), ''), CASE WHEN COALESCE(persons.id, entity_persons.id) IS NOT NULL THEN CONCAT('Contact #', COALESCE(persons.id, entity_persons.id)) ELSE NULL END) as contact_name")
            )
            ->leftJoin('activity_files', 'activities.id', '=', 'activity_files.activity_id')
            ->leftJoin('activity_participants', 'activities.id', '=', 'activity_participants.activity_id')
            ->leftJoin('organization_activities', 'activities.id', '=', 'organization_activities.activity_id')
            ->leftJoin('person_activities', 'activities.id', '=', 'person_activities.activity_id')
            ->leftJoin('persons', 'person_activities.person_id', '=', 'persons.id')
            ->leftJoin('persons as entity_persons', function ($join) {
                $join->on('activities.entity_id', '=', 'entity_persons.id')
                    ->where('activities.entity_type', '=', 'persons');
            })
            ->leftJoin('lead_activities', 'activities.id', '=', 'lead_activities.activity_id')
            ->leftJoin('leads', 'lead_activities.lead_id', '=', 'leads.id')
            ->leftJoin('users', 'activities.user_id', '=', 'users.id')
            ->where(function ($query) {
                if ($userIds = bouncer()->getAuthorizedUserIds()) {
                    $query->whereIn('activities.user_id', $userIds)
                        ->orWhereIn('activity_participants.user_id', $userIds);
                }
            })->groupBy(
                'activities.id',
                'activity_files.id',
                'activity_files.name',
                'activity_files.path',
                'leads.id',
                'users.id',
                'persons.id',
                'entity_persons.id'
            );

        if ($type = request('type')) {
            $queryBuilder->where('activities.type', $type);
        }

        if (request('entity_type') === 'organizations' && request('entity_id')) {
            $organizationId = (int) request('entity_id');
            $contactIds = DB::table('persons')
                ->where('organization_id', $organizationId)
                ->pluck('id')
                ->all();

            $queryBuilder->where(function ($query) use ($organizationId, $contactIds) {
                $query->where(function ($query) use ($organizationId) {
                    $query->where('activities.entity_type', 'organizations')
                        ->where('activities.entity_id', $organizationId);
                })->orWhere('organization_activities.organization_id', $organizationId)
                    ->orWhere('persons.organization_id', $organizationId);

                if (! empty($contactIds)) {
                    $query->orWhere(function ($query) use ($contactIds) {
                        $query->where('activities.entity_type', 'persons')
                            ->whereIn('activities.entity_id', $contactIds);
                    });
                }
            });
        }

        if (request('entity_type') === 'persons' && request('entity_id')) {
            $personId = (int) request('entity_id');

            $queryBuilder->where(function ($query) use ($personId) {
                $query->where(function ($query) use ($personId) {
                    $query->where('activities.entity_type', 'persons')
                        ->where('activities.entity_id', $personId);
                })->orWhere('person_activities.person_id', $personId);
            });
        }

        $this->addFilter('id', 'activities.id');
        $this->addFilter('title', 'activities.title');
        $this->addFilter('schedule_from', 'activities.schedule_from');
        $this->addFilter('created_by', 'users.name');
        $this->addFilter('created_by_id', 'users.name');
        $this->addFilter('created_at', 'activities.created_at');
        $this->addFilter('lead_title', 'leads.title');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        if (request('type') === 'file') {
            $this->prepareFileColumns();

            return;
        }

        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('admin::app.activities.index.datagrid.id'),
            'type'       => 'integer',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'            => 'is_done',
            'label'            => trans('admin::app.activities.index.datagrid.is_done'),
            'type'             => 'string',
            'dropdown_options' => $this->getBooleanDropdownOptions('yes_no'),
            'searchable'       => false,
            'closure'          => fn ($row) => view('admin::activities.datagrid.is-done', compact('row'))->render(),
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('admin::app.activities.index.datagrid.title'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'created_by_id',
            'label'              => trans('admin::app.activities.index.datagrid.created_by'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => UserRepository::class,
                'column'     => [
                    'label' => 'name',
                    'value' => 'name',
                ],
            ],
            'closure'    => function ($row) {
                $route = urldecode(route('admin.settings.users.index', ['id[eq]' => $row->created_by_id]));

                return "<a class='text-brandColor hover:underline' href='".$route."'>".$row->created_by.'</a>';
            },
        ]);

        $this->addColumn([
            'index'   => 'comment',
            'label'   => trans('admin::app.activities.index.datagrid.comment'),
            'type'    => 'string',
        ]);

        $this->addColumn([
            'index'              => 'lead_title',
            'label'              => trans('admin::app.activities.index.datagrid.lead'),
            'type'               => 'string',
            'searchable'         => true,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'searchable_dropdown',
            'filterable_options' => [
                'repository' => LeadRepository::class,
                'column'     => [
                    'label' => 'title',
                    'value' => 'title',
                ],
            ],
            'closure'    => function ($row) {
                if ($row->lead_title == null) {
                    return "<span class='text-gray-800 dark:text-gray-300'>N/A</span>";
                }

                $route = urldecode(route('admin.leads.view', $row->lead_id));

                return "<a class='text-brandColor hover:underline' target='_blank' href='".$route."'>".$row->lead_title.'</a>';
            },
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => trans('admin::app.activities.index.datagrid.type'),
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => true,
            'closure'    => fn ($row) => trans('admin::app.activities.index.datagrid.'.$row->type),
        ]);

        $this->addColumn([
            'index'      => 'schedule_from',
            'label'      => trans('admin::app.activities.index.datagrid.schedule_from'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->schedule_from),
        ]);

        $this->addColumn([
            'index'      => 'schedule_to',
            'label'      => trans('admin::app.activities.index.datagrid.schedule_to'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->schedule_to),
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.activities.index.datagrid.created_at'),
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    protected function prepareFileColumns(): void
    {
        $this->addColumn([
            'index'      => 'file_preview',
            'label'      => 'Preview',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                if (! $row->file_id) {
                    return '<span class="text-gray-400">--</span>';
                }

                $name = trim((string) $row->file_name) ?: (trim((string) $row->title) ?: basename((string) $row->file_path));
                $extension = strtolower(pathinfo($name ?: $row->file_path, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);

                if (! $isImage) {
                    return '<div class="flex h-12 w-12 items-center justify-center rounded-md bg-gray-100 dark:bg-gray-800"><i class="icon-document text-2xl text-gray-400"></i></div>';
                }

                return '<a href="'.e(route('admin.activities.file_preview', $row->file_id)).'" target="_blank" class="block h-12 w-12 overflow-hidden rounded-md bg-gray-100 dark:bg-gray-800"><img src="'.e(route('admin.activities.file_preview', $row->file_id)).'" alt="'.e($name).'" class="h-full w-full object-cover" /></a>';
            },
        ]);

        $this->addColumn([
            'index'      => 'file_name',
            'label'      => 'File Name',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                $name = trim((string) $row->file_name) ?: (trim((string) $row->title) ?: basename((string) $row->file_path));
                $extension = pathinfo((string) $row->file_path, PATHINFO_EXTENSION);
                if ($extension && ! str_contains(basename($name), '.')) {
                    $name .= '.'.$extension;
                }

                return $row->file_id
                    ? '<a class="text-brandColor hover:underline" href="'.e(route('admin.activities.file_download', $row->file_id)).'">'.e($name).'</a>'
                    : e($name ?: '--');
            },
        ]);

        $this->addColumn([
            'index'      => 'created_by_id',
            'label'      => 'Uploaded By',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => fn ($row) => e($row->created_by ?: '--'),
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => 'Date & Time',
            'type'       => 'date',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => fn ($row) => core()->formatDate($row->created_at),
        ]);

        $this->addColumn([
            'index'      => 'contact_name',
            'label'      => 'Contact',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'closure'    => fn ($row) => $row->contact_name ? e($row->contact_name) : '--',
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (request('type') === 'file') {
            $this->addAction([
                'index'  => 'download',
                'icon'   => 'icon-download',
                'title'  => 'Download',
                'method' => 'GET',
                'url'    => fn ($row) => $row->file_id ? route('admin.activities.file_download', $row->file_id) : '#',
            ]);
        }

        if (bouncer()->hasPermission('activities.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.activities.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.activities.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('activities.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.activities.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.activities.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (request('type') === 'file') {
            return;
        }

        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.activities.index.datagrid.mass-delete'),
            'method' => 'POST',
            'url'    => route('admin.activities.mass_delete'),
        ]);

        $this->addMassAction([
            'title'   => trans('admin::app.activities.index.datagrid.mass-update'),
            'url'     => route('admin.activities.mass_update'),
            'method'  => 'POST',
            'options' => [
                [
                    'label' => trans('admin::app.activities.index.datagrid.done'),
                    'value' => 1,
                ], [
                    'label' => trans('admin::app.activities.index.datagrid.not-done'),
                    'value' => 0,
                ],
            ],
        ]);
    }
}
