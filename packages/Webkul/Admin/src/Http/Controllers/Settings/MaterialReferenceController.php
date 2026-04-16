<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Settings\MaterialReferenceDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Contact\Models\Organization;
use Webkul\Product\Models\ColorReference;
use Webkul\Product\Models\MaterialReference;

class MaterialReferenceController extends Controller
{
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(MaterialReferenceDataGrid::class)->process();
        }

        $vendors = Organization::query()
            ->whereIn('type', ['vendor', 'Vendor'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $colorReferences = ColorReference::query()->orderBy('name')->get(['name', 'code']);

        return view('admin::settings.material-references.index', compact('vendors', 'colorReferences'));
    }

    public function store(): JsonResponse
    {
        $data = $this->validateData();

        Event::dispatch('settings.material_reference.create.before');

        $materialReference = MaterialReference::create($this->payload($data));
        $materialReference->vendors()->sync($data['vendor_ids'] ?? []);

        Event::dispatch('settings.material_reference.create.after', $materialReference);

        return response()->json([
            'data' => $materialReference->load('vendors:id,name'),
            'message' => 'Material created successfully.',
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $materialReference = MaterialReference::with('vendors:id,name')->findOrFail($id);

        return response()->json([
            'data' => array_merge($materialReference->toArray(), [
                'vendor_ids' => $materialReference->vendors->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            ]),
        ]);
    }

    public function update(int $id): JsonResponse
    {
        $data = $this->validateData($id);

        Event::dispatch('settings.material_reference.update.before', $id);

        $materialReference = MaterialReference::findOrFail($id);
        $materialReference->update($this->payload($data));
        $materialReference->vendors()->sync($data['vendor_ids'] ?? []);

        Event::dispatch('settings.material_reference.update.after', $materialReference);

        return response()->json([
            'data' => $materialReference->fresh('vendors:id,name'),
            'message' => 'Material updated successfully.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('settings.material_reference.delete.before', $id);
            MaterialReference::findOrFail($id)->delete();
            Event::dispatch('settings.material_reference.delete.after', $id);

            return response()->json(['message' => 'Material deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Material cannot be deleted.'], 400);
        }
    }

    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        foreach ($request->input('indices') as $id) {
            MaterialReference::whereKey($id)->delete();
        }

        return response()->json(['message' => 'Materials deleted successfully.']);
    }

    protected function validateData(?int $id = null): array
    {
        return request()->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('material_references', 'name')->ignore($id)],
            'qty' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:100'],
            'vendor_ids' => ['nullable', 'array'],
            'vendor_ids.*' => ['integer', Rule::exists('organizations', 'id')->where(fn ($query) => $query->whereIn('type', ['vendor', 'Vendor']))],
            'color_name' => ['nullable', 'string', 'max:100'],
            'color_code' => ['nullable', 'string', 'max:20'],
        ]);
    }

    protected function payload(array $data): array
    {
        return [
            'name' => trim((string) $data['name']),
            'qty' => $data['qty'],
            'unit' => trim((string) $data['unit']),
            'color_name' => trim((string) ($data['color_name'] ?? '')) ?: null,
            'color_code' => strtoupper(trim((string) ($data['color_code'] ?? ''))) ?: null,
        ];
    }
}
