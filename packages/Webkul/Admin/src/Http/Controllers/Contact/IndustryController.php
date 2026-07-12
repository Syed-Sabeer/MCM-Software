<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Models\Industry;

class IndustryController extends Controller
{
    public function index(): View
    {
        $industries = Industry::query()
            ->orderBy('name')
            ->paginate(20);

        return view('admin::contacts.industries.index', compact('industries'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validateIndustry($request);

        $industry = Industry::query()->firstOrCreate($data);

        if ($request->expectsJson()) {
            return response()->json([
                'id'      => $industry->id,
                'name'    => $industry->name,
                'message' => 'Industry saved successfully.',
            ]);
        }

        session()->flash('success', 'Industry saved successfully.');

        return back();
    }

    public function edit(int $id): View
    {
        $industry = Industry::query()->findOrFail($id);

        return view('admin::contacts.industries.edit', compact('industry'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $industry = Industry::query()->findOrFail($id);
        $industry->update($this->validateIndustry($request, $id));

        session()->flash('success', 'Industry updated successfully.');

        return redirect()->route($this->routePrefix() . '.organizations.industries.index');
    }

    public function destroy(int $id): RedirectResponse|JsonResponse
    {
        Industry::query()->findOrFail($id)->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Industry deleted successfully.']);
        }

        session()->flash('success', 'Industry deleted successfully.');

        return back();
    }

    protected function validateIndustry(Request $request, ?int $ignoreId = null): array
    {
        $rules = ['required', 'string', 'max:255'];

        if ($ignoreId) {
            $rules[] = 'unique:industries,name,' . $ignoreId;
        }

        return $request->validate([
            'name' => $rules,
        ]);
    }

    protected function routePrefix(): string
    {
        $routeName = request()->route()?->getName() ?? '';

        if (str_contains($routeName, 'admin.customers.')) {
            return 'admin.customers';
        }

        if (str_contains($routeName, 'admin.vendors.')) {
            return 'admin.vendors';
        }

        return 'admin.contacts';
    }
}
