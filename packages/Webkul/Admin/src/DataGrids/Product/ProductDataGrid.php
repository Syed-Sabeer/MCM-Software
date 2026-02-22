<?php

namespace Webkul\Admin\DataGrids\Product;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\DataGrid\DataGrid;
use Webkul\Tag\Repositories\TagRepository;

class ProductDataGrid extends DataGrid
{
    /**
     * Whether the products table has publish_on_website column.
     */
    protected bool $hasPublishColumn = false;

    /**
     * Prepare query builder.
     */
    public function prepareQueryBuilder(): Builder
    {
        $tablePrefix = DB::getTablePrefix();
        $this->hasPublishColumn = Schema::hasColumn('products', 'publish_on_website');

        $selects = [
            'products.id',
            'products.sku',
            'products.name',
            'products.price',
            'products.cover_image',
            'products.style',
            'products.size',
        ];
        if ($this->hasPublishColumn) {
            $selects[] = 'products.publish_on_website';
        }

        $groupBy = ['products.id', 'products.sku', 'products.name', 'products.price', 'products.cover_image', 'products.style', 'products.size'];
        if ($this->hasPublishColumn) {
            $groupBy[] = 'products.publish_on_website';
        }

        $queryBuilder = DB::table('products')
            ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
            ->leftJoin('product_inventories', 'products.id', '=', 'product_inventories.product_id')
            ->leftJoin('product_tags', 'products.id', '=', 'product_tags.product_id')
            ->leftJoin('tags', 'tags.id', '=', 'product_tags.tag_id')
            ->select($selects)
            ->addSelect(DB::raw('MAX('.$tablePrefix.'product_categories.name) as category_name'))
            ->addSelect(DB::raw('MAX('.$tablePrefix.'tags.name) as tag_name'))
            ->addSelect(DB::raw('SUM('.$tablePrefix.'product_inventories.in_stock) as total_in_stock'))
            ->addSelect(DB::raw('SUM('.$tablePrefix.'product_inventories.allocated) as total_allocated'))
            ->addSelect(DB::raw('SUM('.$tablePrefix.'product_inventories.in_stock - '.$tablePrefix.'product_inventories.allocated) as total_on_hand'))
            ->groupBy($groupBy);

        if (request()->route('id')) {
            $queryBuilder->where('product_inventories.warehouse_id', request()->route('id'));
        }

        $this->addFilter('id', 'products.id');
        $this->addFilter('total_in_stock', DB::raw('SUM('.$tablePrefix.'product_inventories.in_stock'));
        $this->addFilter('total_allocated', DB::raw('SUM('.$tablePrefix.'product_inventories.allocated'));
        $this->addFilter('total_on_hand', DB::raw('SUM('.$tablePrefix.'product_inventories.in_stock - '.$tablePrefix.'product_inventories.allocated'));
        $this->addFilter('tag_name', 'tags.name');
        $this->addFilter('category_name', 'product_categories.name');

        return $queryBuilder;
    }

    /**
     * Add columns.
     */
    public function prepareColumns(): void
    {
        $this->addColumn([
            'index'   => 'cover_image',
            'label'   => trans('admin::app.products.index.datagrid.cover-image'),
            'type'    => 'string',
            'sortable' => false,
            'searchable' => false,
            'closure' => function ($row) {
                if (empty($row->cover_image)) {
                    return '<span class="text-gray-400">—</span>';
                }
                $url = asset('storage/' . $row->cover_image);

                return '<img src="'.e($url).'" alt="" class="h-10 w-10 rounded border border-gray-200 object-cover dark:border-gray-700" loading="lazy" />';
            },
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.products.index.datagrid.name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'   => 'category_name',
            'label'   => trans('admin::app.products.index.datagrid.category'),
            'type'    => 'string',
            'sortable' => true,
            'searchable' => false,
            'filterable' => true,
            'closure' => fn ($row) => $row->category_name ?? '—',
        ]);

        $this->addColumn([
            'index'   => 'style',
            'label'   => trans('admin::app.products.index.datagrid.style'),
            'type'    => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => fn ($row) => $row->style ?? '—',
        ]);

        $this->addColumn([
            'index'   => 'size',
            'label'   => trans('admin::app.products.index.datagrid.size'),
            'type'    => 'string',
            'sortable' => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => fn ($row) => $row->size ?? '—',
        ]);

        if ($this->hasPublishColumn) {
            $this->addColumn([
                'index'   => 'publish_on_website',
                'label'   => trans('admin::app.products.index.datagrid.publish-on-website'),
                'type'    => 'string',
                'sortable' => true,
                'searchable' => false,
                'closure' => function ($row) {
                    $url = route('admin.products.toggle_publish', ['id' => $row->id]);
                    $checked = ! empty($row->publish_on_website) ? ' checked' : '';
                    $csrf = csrf_token();

                    return '<label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" class="product-publish-toggle peer sr-only" data-url="'.e($url).'" data-csrf="'.e($csrf).'"'.$checked.'>
                        <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[\'\'] peer-checked:bg-brandColor peer-checked:after:translate-x-full peer-focus:outline-none dark:bg-gray-700 dark:peer-checked:bg-brandColor"></div>
                    </label>';
                },
            ]);
        }
    }

    /**
     * Prepare actions.
     */
    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('products.view')) {
            $this->addAction([
                'index'  => 'view',
                'icon'   => 'icon-eye',
                'title'  => trans('admin::app.products.index.datagrid.view'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.products.view', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('products.edit')) {
            $this->addAction([
                'index'  => 'edit',
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.products.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => fn ($row) => route('admin.products.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('products.create')) {
            $this->addAction([
                'index'  => 'duplicate',
                'icon'   => 'icon-add',
                'title'  => trans('admin::app.products.index.datagrid.duplicate'),
                'method' => 'POST',
                'url'    => fn ($row) => route('admin.products.duplicate', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('products.delete')) {
            $this->addAction([
                'index'  => 'delete',
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.products.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => fn ($row) => route('admin.products.delete', $row->id),
            ]);
        }
    }

    /**
     * Prepare mass actions.
     */
    public function prepareMassActions(): void
    {
        $this->addMassAction([
            'icon'   => 'icon-delete',
            'title'  => trans('admin::app.products.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => route('admin.products.mass_delete'),
        ]);
    }
}
