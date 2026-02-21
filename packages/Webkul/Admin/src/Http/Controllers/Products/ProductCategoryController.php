<?php

namespace Webkul\Admin\Http\Controllers\Products;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Product\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     */
    public function index(): View
    {
        $categories = ProductCategory::orderBy('name')->get();

        return view('admin::products.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin::products.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(): RedirectResponse
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        ProductCategory::create($data);

        session()->flash('success', __('admin::app.products.categories.created'));

        return redirect()->route('admin.product_categories.index');
    }

    /**
     * Show the form for editing the category.
     */
    public function edit(int $id): View
    {
        $category = ProductCategory::findOrFail($id);

        return view('admin::products.categories.edit', compact('category'));
    }

    /**
     * Update the category.
     */
    public function update(int $id): RedirectResponse
    {
        $category = ProductCategory::findOrFail($id);

        $data = request()->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $category->update($data);

        session()->flash('success', __('admin::app.products.categories.updated'));

        return redirect()->route('admin.product_categories.index');
    }

    /**
     * Remove the category.
     */
    public function destroy(int $id): RedirectResponse
    {
        $category = ProductCategory::findOrFail($id);
        $category->delete();

        session()->flash('success', __('admin::app.products.categories.deleted'));

        return redirect()->route('admin.product_categories.index');
    }
}
