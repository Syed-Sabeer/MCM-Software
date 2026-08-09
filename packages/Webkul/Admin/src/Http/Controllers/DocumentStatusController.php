<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\DocumentStatusDataGrid;
use Webkul\Admin\Support\DocumentStatusOptions;

class DocumentStatusController extends Controller
{
    public function index(string $type): JsonResponse|View
    {
        $this->ensureValidType($type);

        if (request()->ajax()) {
            return datagrid(DocumentStatusDataGrid::class)->process();
        }

        return view('admin::document-statuses.index', compact('type'));
    }

    public function store(Request $request, string $type): JsonResponse|RedirectResponse
    {
        $this->ensureValidType($type);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $value = Str::slug($data['name'], '_');

        if ($value === '') {
            return $this->failedResponse($request, 'Please enter a valid status name.');
        }

        $exists = DB::table('document_statuses')
            ->where('type', $type)
            ->where('value', $value)
            ->exists();

        if ($exists) {
            return $this->failedResponse($request, 'This status already exists.');
        }

        $id = DB::table('document_statuses')->insertGetId([
            'type'       => $type,
            'name'       => $data['name'],
            'value'      => $value,
            'sort_order' => (int) DB::table('document_statuses')->where('type', $type)->max('sort_order') + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()
                ->route('admin.document_statuses.index', $type)
                ->with('success', 'Status added successfully.');
        }

        return response()->json([
            'status'   => DB::table('document_statuses')->where('id', $id)->first(['id', 'name', 'value']),
            'statuses' => DocumentStatusOptions::all($type),
            'message'  => 'Status added successfully.',
        ]);
    }

    public function update(Request $request, string $type, int $id): JsonResponse|RedirectResponse
    {
        $this->ensureValidType($type);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $updated = DB::table('document_statuses')
            ->where('type', $type)
            ->where('id', $id)
            ->update([
                'name'       => $data['name'],
                'updated_at' => now(),
            ]);

        if (! $updated) {
            abort(404);
        }

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()
                ->route('admin.document_statuses.index', $type)
                ->with('success', 'Status updated successfully.');
        }

        return response()->json([
            'statuses' => DocumentStatusOptions::all($type),
            'message'  => 'Status updated successfully.',
        ]);
    }

    public function destroy(Request $request, string $type, int $id): JsonResponse|RedirectResponse
    {
        $this->ensureValidType($type);

        $deleted = DB::table('document_statuses')
            ->where('type', $type)
            ->where('id', $id)
            ->delete();

        if (! $deleted) {
            abort(404);
        }

        if (! $request->expectsJson() && ! $request->ajax()) {
            return redirect()
                ->route('admin.document_statuses.index', $type)
                ->with('success', 'Status deleted successfully.');
        }

        return response()->json([
            'statuses' => DocumentStatusOptions::all($type),
            'message'  => 'Status deleted successfully.',
        ]);
    }

    protected function ensureValidType(string $type): void
    {
        validator(
            ['type' => $type],
            ['type' => ['required', Rule::in(DocumentStatusOptions::allowedTypes())]]
        )->validate();
    }

    protected function failedResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $message], 422);
        }

        return redirect()->back()->withInput()->withErrors(['name' => $message]);
    }
}
