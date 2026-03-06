<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\Contact\OrganizationDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Contact\Repositories\OrganizationRepository;

class OrganizationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected OrganizationRepository $organizationRepository)
    {
        request()->request->add(['entity_type' => 'organizations']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(OrganizationDataGrid::class)->process();
        }

        return view('admin::contacts.organizations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin::contacts.organizations.create');
    }

    /**
     * Fetch organization as JSON (for AJAX/API use in lead create, etc.).
     */
    public function fetch(int $id): JsonResponse
    {
        $organization = $this->organizationRepository->find($id);

        if (!$organization) {
            return response()->json(['message' => 'Organization not found'], 404);
        }

        return response()->json([
            'id'   => $organization->id,
            'name' => $organization->name,
        ]);
    }

    /**
     * Display the specified organization (detail page).
     */
    public function show(int $id): View
    {
        $organization = $this->organizationRepository->findOrFail($id);

        return view('admin::contacts.organizations.view', compact('organization'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        $request->validate([
            'description'      => ['nullable', 'max:100'],
            'billing_street'   => ['nullable', 'max:100'],
            'shipping_street'  => ['nullable', 'max:100'],
        ]);

        $data = $request->all();

        if ($request->boolean('same_as_billing')) {
            $data['shipping_street']   = $data['billing_street']   ?? null;
            $data['shipping_city']     = $data['billing_city']     ?? null;
            $data['shipping_state']    = $data['billing_state']    ?? null;
            $data['shipping_postcode'] = $data['billing_postcode'] ?? null;
            $data['shipping_country']  = $data['billing_country']  ?? null;
        }

        Event::dispatch('contacts.organization.create.before');

        $organization = $this->organizationRepository->create($data);

        Event::dispatch('contacts.organization.create.after', $organization);

        session()->flash('success', trans('admin::app.contacts.organizations.index.create-success'));

        return redirect()->route('admin.contacts.organizations.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $organization = $this->organizationRepository->findOrFail($id);

        return view('admin::contacts.organizations.edit', compact('organization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        $request->validate([
            'description'      => ['nullable', 'max:100'],
            'billing_street'   => ['nullable', 'max:100'],
            'shipping_street'  => ['nullable', 'max:100'],
        ]);

        $data = $request->all();

        if ($request->boolean('same_as_billing')) {
            $data['shipping_street']   = $data['billing_street']   ?? null;
            $data['shipping_city']     = $data['billing_city']     ?? null;
            $data['shipping_state']    = $data['billing_state']    ?? null;
            $data['shipping_postcode'] = $data['billing_postcode'] ?? null;
            $data['shipping_country']  = $data['billing_country']  ?? null;
        }

        Event::dispatch('contacts.organization.update.before', $id);

        $organization = $this->organizationRepository->update($data, $id);

        Event::dispatch('contacts.organization.update.after', $organization);

        session()->flash('success', trans('admin::app.contacts.organizations.index.update-success'));

        return redirect()->route('admin.contacts.organizations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('contact.organization.delete.before', $id);

            $this->organizationRepository->delete($id);

            Event::dispatch('contact.organization.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.contacts.organizations.index.delete-success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.contacts.organizations.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     */
    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $organizations = $this->organizationRepository->findWhereIn('id', $massDestroyRequest->input('indices'));

        foreach ($organizations as $organization) {
            Event::dispatch('contact.organization.delete.before', $organization);

            $this->organizationRepository->delete($organization->id);

            Event::dispatch('contact.organization.delete.after', $organization);
        }

        return response()->json([
            'message' => trans('admin::app.contacts.organizations.index.delete-success'),
        ]);
    }
}
