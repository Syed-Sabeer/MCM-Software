<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\ColorReferenceDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Product\Models\ColorReference;

class ColorReferenceController extends Controller
{
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(ColorReferenceDataGrid::class)->process();
        }

        return view('admin::settings.color-references.index');
    }

    public function store(): JsonResponse
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:100', 'unique:color_references,name'],
            'code' => ['required', 'string', 'max:20'],
        ]);

        Event::dispatch('settings.color_reference.create.before');

        $colorReference = ColorReference::create([
            'name' => trim((string) $data['name']),
            'code' => strtoupper(trim((string) $data['code'])),
        ]);

        Event::dispatch('settings.color_reference.create.after', $colorReference);

        return response()->json([
            'data'    => $colorReference,
            'message' => 'Color created successfully.',
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        return response()->json([
            'data' => ColorReference::findOrFail($id),
        ]);
    }

    public function update(int $id): JsonResponse
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:100', 'unique:color_references,name,' . $id],
            'code' => ['required', 'string', 'max:20'],
        ]);

        Event::dispatch('settings.color_reference.update.before', $id);

        $colorReference = ColorReference::findOrFail($id);
        $colorReference->update([
            'name' => trim((string) $data['name']),
            'code' => strtoupper(trim((string) $data['code'])),
        ]);

        Event::dispatch('settings.color_reference.update.after', $colorReference);

        return response()->json([
            'data'    => $colorReference,
            'message' => 'Color updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('settings.color_reference.delete.before', $id);

            ColorReference::findOrFail($id)->delete();

            Event::dispatch('settings.color_reference.delete.after', $id);

            return response()->json([
                'message' => 'Color deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Color cannot be deleted.',
            ], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            ColorReference::whereKey($id)->delete();
        }

        return response()->json([
            'message' => 'Colors deleted successfully.',
        ]);
    }
}
