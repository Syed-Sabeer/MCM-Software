<?php

namespace Webkul\Admin\DataGrids\Activity;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class TaskDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $userId = auth()->guard('user')->id();

        $queryBuilder = DB::table('activities')
            ->leftJoin('activity_participants', 'activities.id', '=', 'activity_participants.activity_id')
            ->leftJoin('users', 'activities.user_id', '=', 'users.id')
            ->select(
                'activities.id',
                'activities.title',
                'activities.comment',
                'activities.schedule_from',
                'activities.schedule_to',
                'activities.is_done',
                'activities.created_at',
                'users.name as owner_name'
            )
            ->where('activities.type', 'task')
            ->where(function ($query) use ($userId) {
                $query->where('activities.user_id', $userId)
                    ->orWhere('activity_participants.user_id', $userId);
            })
            ->groupBy(
                'activities.id',
                'activities.title',
                'activities.comment',
                'activities.schedule_from',
                'activities.schedule_to',
                'activities.is_done',
                'activities.created_at',
                'users.name'
            );

        $this->addFilter('id', 'activities.id');
        $this->addFilter('title', 'activities.title');
        $this->addFilter('owner_name', 'users.name');
        $this->addFilter('schedule_from', 'activities.schedule_from');
        $this->addFilter('is_done', 'activities.is_done');
        $this->addFilter('created_at', 'activities.created_at');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'ID',
            'type'       => 'integer',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => 'Task',
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                $title = e($row->title ?: 'Untitled task');
                $url = route('admin.activities.edit', $row->id);

                return "<a class='text-brandColor hover:underline font-medium' href='{$url}'>{$title}</a>";
            },
        ]);

        $this->addColumn([
            'index'   => 'comment',
            'label'   => 'Details',
            'type'    => 'string',
            'closure' => fn ($row) => e($row->comment ? \Illuminate\Support\Str::limit($row->comment, 140) : '-'),
        ]);

        $this->addColumn([
            'index'      => 'owner_name',
            'label'      => 'Assigned By',
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => fn ($row) => e($row->owner_name ?: '-'),
        ]);

        $this->addColumn([
            'index'      => 'schedule_from',
            'label'      => 'Due Date',
            'type'       => 'date',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => fn ($row) => $row->schedule_from ? core()->formatDate($row->schedule_from) : '-',
        ]);

        $this->addColumn([
            'index'              => 'is_done',
            'label'              => 'Status',
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                ['label' => 'Open', 'value' => 0],
                ['label' => 'Done', 'value' => 1],
            ],
            'closure'            => function ($row) {
                if ((int) $row->is_done === 1) {
                    return "<span class='rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'>Done</span>";
                }

                if (! empty($row->schedule_from) && now()->gt(\Carbon\Carbon::parse($row->schedule_from))) {
                    return "<span class='rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'>Overdue</span>";
                }

                return "<span class='rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'>Open</span>";
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => 'Created',
            'type'       => 'date',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => fn ($row) => core()->formatDate($row->created_at),
        ]);
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('activities.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.activities.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.activities.edit', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        if (bouncer()->hasPermission('activities.edit')) {
            $this->addMassAction([
                'title'   => trans('admin::app.activities.index.datagrid.mass-update'),
                'url'     => route('admin.activities.mass_update'),
                'method'  => 'POST',
                'options' => [
                    [
                        'label' => trans('admin::app.activities.index.datagrid.done'),
                        'value' => 1,
                    ],
                    [
                        'label' => trans('admin::app.activities.index.datagrid.not-done'),
                        'value' => 0,
                    ],
                ],
            ]);
        }
    }
}
