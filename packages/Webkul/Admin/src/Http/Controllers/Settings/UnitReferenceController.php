<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\UnitReferenceDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Product\Models\UnitReference;

class UnitReferenceController extends Controller
{
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(UnitReferenceDataGrid::class)->process();
        }

        return view('admin::settings.units.index');
    }

    public function store(): JsonResponse
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:100', 'unique:unit_references,name'],
        ]);

        Event::dispatch('settings.unit_reference.create.before');

        $unitReference = UnitReference::create([
            'name' => strtoupper(trim((string) $data['name'])),
        ]);

        Event::dispatch('settings.unit_reference.create.after', $unitReference);

        return response()->json([
            'data'    => $unitReference,
            'message' => 'Unit created successfully.',
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        return response()->json([
            'data' => UnitReference::findOrFail($id),
        ]);
    }

    public function update(int $id): JsonResponse
    {
        $data = request()->validate([
            'name' => ['required', 'string', 'max:100', 'unique:unit_references,name,' . $id],
        ]);

        Event::dispatch('settings.unit_reference.update.before', $id);

        $unitReference = UnitReference::findOrFail($id);
        $unitReference->update([
            'name' => strtoupper(trim((string) $data['name'])),
        ]);

        Event::dispatch('settings.unit_reference.update.after', $unitReference);

        return response()->json([
            'data'    => $unitReference,
            'message' => 'Unit updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('settings.unit_reference.delete.before', $id);

            UnitReference::findOrFail($id)->delete();

            Event::dispatch('settings.unit_reference.delete.after', $id);

            return response()->json([
                'message' => 'Unit deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Unit cannot be deleted.',
            ], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            UnitReference::whereKey($id)->delete();
        }

        return response()->json([
            'message' => 'Units deleted successfully.',
        ]);
    }
}
