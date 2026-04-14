<?php

namespace Webkul\Admin\Http\Controllers\Contact\Persons;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\DataGrids\Contact\PersonDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\PersonResource;
use Webkul\Contact\Repositories\PersonRepository;

class PersonController extends Controller
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(protected PersonRepository $personRepository)
    {
        request()->request->add(['entity_type' => 'persons']);
    }

    /**
     * Get the expected organization type based on route prefix.
     */
    protected function getRouteType(): ?string
    {
        $routeName = request()->route()?->getName() ?? '';

        if (str_contains($routeName, 'admin.customers.')) {
            return 'customer';
        }

        if (str_contains($routeName, 'admin.vendors.')) {
            return 'vendor';
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Detect organization type from current route and inject into query for DataGrid filtering
        if ($type = $this->getRouteType()) {
            if (!request()->query('type')) {
                request()->query->set('type', $type);
            }
        }

        if (request()->ajax()) {
            return datagrid(PersonDataGrid::class)->process();
        }

        return view('admin::contacts.persons.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $organization = null;

        if ($organizationId = request()->integer('organization_id')) {
            $organization = app(\Webkul\Contact\Repositories\OrganizationRepository::class)->find($organizationId);
        }

        $routeType = $this->getRouteType();

        return view('admin::contacts.persons.create', compact('organization', 'routeType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse|JsonResponse
    {
        Event::dispatch('contacts.person.create.before');

        $data = $request->all();

        if ($routeType = $this->getRouteType()) {
            $data['type'] = $routeType;
        }

        $person = $this->personRepository->create($data);

        Event::dispatch('contacts.person.create.after', $person);

        if (request()->ajax()) {
            return response()->json([
                'data'    => $person,
                'message' => trans('admin::app.contacts.persons.index.create-success'),
            ]);
        }

        $successMessage = trans('admin::app.contacts.persons.index.create-success');
        $organizationId = $request->input('organization_id');

        if ($request->input('save_action') === 'new') {
            return redirect()
                ->route('admin.contacts.persons.create', array_filter([
                    'organization_id' => $organizationId,
                ]))
                ->with('success', $successMessage);
        }

        // Redirect to appropriate route based on organization type
        $routeName = request()->route()?->getName() ?? '';
        if ($organizationId) {
            if (str_contains($routeName, 'admin.customers.')) {
                return redirect()
                    ->route('admin.customers.organizations.view', $organizationId)
                    ->with('success', $successMessage);
            } elseif (str_contains($routeName, 'admin.vendors.')) {
                return redirect()
                    ->route('admin.vendors.organizations.view', $organizationId)
                    ->with('success', $successMessage);
            }

            return redirect()
                ->route('admin.contacts.organizations.view', $organizationId)
                ->with('success', $successMessage);
        }

        session()->flash('success', $successMessage);

        // Redirect to appropriate list based on route
        if (str_contains($routeName, 'admin.customers.')) {
            return redirect()->route('admin.customers.persons.index');
        } elseif (str_contains($routeName, 'admin.vendors.')) {
            return redirect()->route('admin.vendors.persons.index');
        }

        return redirect()->route('admin.contacts.persons.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $person = $this->personRepository->with('attribute_values')->findOrFail($id);

        return view('admin::contacts.persons.view', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $person = $this->personRepository->findOrFail($id);

        $routeType = $this->getRouteType();

        return view('admin::contacts.persons.edit', compact('person', 'routeType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse|JsonResponse
    {
        Event::dispatch('contacts.person.update.before', $id);

        $data = $request->all();

        if ($routeType = $this->getRouteType()) {
            $data['type'] = $routeType;
        }

        $person = $this->personRepository->update($data, $id);

        Event::dispatch('contacts.person.update.after', $person);

        if (request()->ajax()) {
            return response()->json([
                'data'    => $person,
                'message' => trans('admin::app.contacts.persons.index.update-success'),
            ], 200);
        }

        session()->flash('success', trans('admin::app.contacts.persons.index.update-success'));

        $routeName = request()->route()?->getName() ?? '';

        if (str_contains($routeName, 'admin.customers.')) {
            return redirect()->route('admin.customers.persons.index');
        }

        if (str_contains($routeName, 'admin.vendors.')) {
            return redirect()->route('admin.vendors.persons.index');
        }

        return redirect()->route('admin.contacts.persons.index');
    }

    /**
     * Search person results.
     */
    public function search(): JsonResource
    {
        $query = trim(request()->string('query')->toString());

        $personsQuery = $this->personRepository->pushCriteria(app(RequestCriteria::class));

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $personsQuery = $personsQuery->whereIn('user_id', $userIds);
        }

        if ($query !== '') {
            $personsQuery = $personsQuery->where(function ($q) use ($query) {
                $q->whereRaw("LOWER(TRIM(CONCAT_WS(' ', first_name, last_name))) LIKE ?", ['%' . mb_strtolower($query) . '%'])
                  ->orWhereRaw("LOWER(name) LIKE ?", ['%' . mb_strtolower($query) . '%']);
            });
        }

        $persons = $personsQuery->get();

        return PersonResource::collection($persons);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $person = $this->personRepository->findOrFail($id);

        if (
            $person->leads
            && $person->leads->count() > 0
        ) {
            return response()->json([
                'message' => trans('admin::app.contacts.persons.index.delete-failed'),
            ], 400);
        }

        try {
            Event::dispatch('contacts.person.delete.before', $person);

            $person->delete();

            Event::dispatch('contacts.person.delete.after', $person);

            return response()->json([
                'message' => trans('admin::app.contacts.persons.index.delete-success'),
            ], 200);

        } catch (Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.contacts.persons.index.delete-failed'),
            ], 400);
        }
    }

    /**
     * Mass destroy the specified resources from storage.
     */
    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        try {
            $persons = $this->personRepository->findWhereIn('id', $request->input('indices', []));

            $deletedCount = 0;

            $blockedCount = 0;

            foreach ($persons as $person) {
                if (
                    $person->leads
                    && $person->leads->count() > 0
                ) {
                    $blockedCount++;

                    continue;
                }

                Event::dispatch('contact.person.delete.before', $person);

                $this->personRepository->delete($person->id);

                Event::dispatch('contact.person.delete.after', $person);

                $deletedCount++;
            }

            $statusCode = 200;

            switch (true) {
                case $deletedCount > 0 && $blockedCount === 0:
                    $message = trans('admin::app.contacts.persons.index.all-delete-success');

                    break;

                case $deletedCount > 0 && $blockedCount > 0:
                    $message = trans('admin::app.contacts.persons.index.partial-delete-warning');

                    break;

                case $deletedCount === 0 && $blockedCount > 0:
                    $message = trans('admin::app.contacts.persons.index.none-delete-warning');

                    $statusCode = 400;

                    break;

                default:
                    $message = trans('admin::app.contacts.persons.index.no-selection');

                    $statusCode = 400;

                    break;
            }

            return response()->json(['message' => $message], $statusCode);
        } catch (Exception $exception) {
            return response()->json([
                'message' => trans('admin::app.contacts.persons.index.delete-failed'),
            ], 400);
        }
    }
}
