<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class DocumentStatusDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        $type = (string) request()->route('statusType');

        $queryBuilder = DB::table('document_statuses')
            ->where('type', $type)
            ->select([
                'id',
                'name',
                'sort_order',
                'updated_at',
            ]);

        $this->addFilter('id', 'id');
        $this->addFilter('name', 'name');
        $this->addFilter('updated_at', 'updated_at');

        return $queryBuilder;
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'ID',
            'type'       => 'integer',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => 'Status Name',
            'type'       => 'string',
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'           => 'updated_at',
            'label'           => 'Updated At',
            'type'            => 'date',
            'sortable'        => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
            'closure'         => fn ($row) => $row->updated_at ? core()->formatDate($row->updated_at) : '-',
        ]);
    }

    public function prepareActions(): void
    {
        $type = (string) request()->route('statusType');

        $this->addAction([
            'index'  => 'edit',
            'icon'   => 'icon-edit',
            'title'  => 'Edit',
            'method' => 'PUT',
            'url'    => fn ($row) => route('admin.document_statuses.update', [$type, $row->id]),
        ]);

        $this->addAction([
            'index'  => 'delete',
            'icon'   => 'icon-delete',
            'title'  => 'Delete',
            'method' => 'DELETE',
            'url'    => fn ($row) => route('admin.document_statuses.delete', [$type, $row->id]),
        ]);
    }
}
