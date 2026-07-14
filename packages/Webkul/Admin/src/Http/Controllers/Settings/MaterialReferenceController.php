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
use Webkul\Product\Models\MaterialReference;
use Webkul\Product\Models\UnitReference;

class MaterialReferenceController extends Controller
{
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(MaterialReferenceDataGrid::class)->process();
        }

        return $this->renderIndex();
    }

    protected function renderIndex(?int $editId = null): View
    {
        $vendors = Organization::query()
            ->whereIn('type', ['vendor', 'Vendor'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $units = UnitReference::query()
            ->orderBy('name')
            ->get(['name']);

        return view('admin::settings.material-references.index', compact('vendors', 'units', 'editId'));
    }

    public function store(): JsonResponse
    {
        $data = $this->validateData();

        Event::dispatch('settings.material_reference.create.before');

        $materialReference = MaterialReference::create($this->payload($data));
        $materialReference->vendors()->sync($data['vendor_ids'] ?? []);

        Event::dispatch('settings.material_reference.create.after', $materialReference);

        return response()->json([
            'data' => $materialReference->load('vendors'),
            'message' => 'Material created successfully.',
        ]);
    }

    public function edit(int $id): View|JsonResponse
    {
        if (! request()->expectsJson() && ! request()->ajax()) {
            return $this->renderIndex($id);
        }

        $materialReference = MaterialReference::with('vendors')->findOrFail($id);

        return response()->json([
            'data' => array_merge($materialReference->toArray(), [
                'qty' => $this->formatQty($materialReference->qty),
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
            'data' => $materialReference->fresh('vendors'),
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
            'qty' => ['required', 'numeric', 'min:0', 'decimal:0,3'],
            'unit' => ['required', 'string', 'max:100', Rule::exists('unit_references', 'name')],
            'vendor_ids' => ['nullable', 'array'],
            'vendor_ids.*' => ['integer', Rule::exists('organizations', 'id')->where(fn ($query) => $query->whereIn('type', ['vendor', 'Vendor']))],
        ]);
    }

    protected function payload(array $data): array
    {
        return [
            'name' => trim((string) $data['name']),
            'qty' => round((float) $data['qty'], 3),
            'unit' => trim((string) $data['unit']),
        ];
    }

    protected function formatQty(mixed $qty): string
    {
        return rtrim(rtrim(number_format((float) $qty, 3, '.', ''), '0'), '.') ?: '0';
    }
}
