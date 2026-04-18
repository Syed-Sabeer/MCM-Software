<?php

namespace Webkul\Admin\Http\Controllers\Contact\Persons;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\DataGrids\Contact\PersonDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\PersonResource;
use Webkul\Admin\Notifications\User\Create as UserCreatedNotification;
use Webkul\Contact\Repositories\OrganizationRepository;
use Webkul\Contact\Repositories\PersonRepository;
use Webkul\User\Repositories\RoleRepository;
use Webkul\User\Repositories\UserRepository;

class PersonController extends Controller
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(
        protected PersonRepository $personRepository,
        protected OrganizationRepository $organizationRepository,
        protected RoleRepository $roleRepository,
        protected UserRepository $userRepository
    ) {
        request()->request->add(['entity_type' => 'persons']);
    }

    /**
     * Get the expected organization type based on route prefix.
     */
    protected function getRouteType(): ?string
    {
        $routeName = request()->route()?->getName() ?? '';

        if (str_contains($routeName, 'admin.employees.')) {
            return 'employee';
        }

        if (str_contains($routeName, 'admin.customers.')) {
            return 'customer';
        }

        if (str_contains($routeName, 'admin.vendors.')) {
            return 'vendor';
        }

        $requestedType = strtolower((string) request()->query('type', request()->input('type')));

        return in_array($requestedType, ['customer', 'vendor', 'employee'], true)
            ? $requestedType
            : null;
    }

    /**
     * Resolve route prefix for redirects and view state.
     */
    protected function getRoutePrefix(): string
    {
        $routeName = request()->route()?->getName() ?? '';

        if (str_contains($routeName, 'admin.employees.')) {
            return 'employees';
        }

        if (str_contains($routeName, 'admin.customers.')) {
            return 'customers';
        }

        if (str_contains($routeName, 'admin.vendors.')) {
            return 'vendors';
        }

        return 'contacts';
    }

    /**
     * Fetch roles available for employees.
     */
    protected function getEmployeeRoles()
    {
        return $this->roleRepository
            ->scopeQuery(function ($query) {
                return $query
                    ->where('description', 'like', '%Employee%')
                    ->orderBy('name');
            })
            ->all();
    }

    /**
     * Determine whether the request is for an employee.
     */
    protected function isEmployeeContext(array $data = []): bool
    {
        return ($this->getRouteType() === 'employee')
            || strtolower((string) ($data['type'] ?? '')) === 'employee';
    }

    /**
     * Validate employee-specific fields.
     */
    protected function validateEmployeeFields(AttributeForm $request, ?int $personId = null): void
    {
        $roleIds = $this->getEmployeeRoles()->pluck('id')->all();
        $linkedUserId = null;

        if ($personId) {
            $linkedUserId = $this->personRepository->findOrFail($personId)?->user_id;
        }

        $request->validate([
            'full_name'         => ['required', 'string', 'max:150'],
            'email'             => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($linkedUserId),
            ],
            'phone'             => ['nullable', 'string', 'max:50'],
            'mailing_street'    => ['nullable', 'string', 'max:255'],
            'mailing_city'      => ['nullable', 'string', 'max:100'],
            'mailing_state'     => ['nullable', 'string', 'max:100'],
            'mailing_postcode'  => ['nullable', 'string', 'max:30'],
            'mailing_country'   => ['nullable', 'string', 'max:100'],
            'birth_date'        => ['nullable', 'date'],
            'role_id'           => ['required', 'integer', Rule::in($roleIds)],
        ]);
    }

    /**
     * Normalize person name payload.
     */
    protected function normalizeNameFields(array $data): array
    {
        $fullName = trim((string) ($data['full_name'] ?? ''));

        if ($fullName !== '') {
            $parts = preg_split('/\s+/', $fullName, 2) ?: [];

            $data['name'] = $fullName;
            $data['first_name'] = $parts[0] ?? $fullName;
            $data['last_name'] = $parts[1] ?? '';
        } else {
            $firstName = trim((string) ($data['first_name'] ?? ''));
            $lastName = trim((string) ($data['last_name'] ?? ''));
            $data['name'] = trim($firstName.' '.$lastName);
        }

        return $data;
    }

    /**
     * Create or update the linked employee login user.
     */
    protected function syncEmployeeUser(array $data, ?int $existingUserId = null): array
    {
        $plainPassword = null;
        $wasCreated = false;

        $userPayload = [
            'name'            => $data['name'],
            'email'           => $data['email'],
            'role_id'         => $data['role_id'],
            'status'          => 1,
            'view_permission' => 'global',
        ];

        if ($existingUserId) {
            $user = $this->userRepository->update($userPayload, $existingUserId);
        } else {
            $plainPassword = Str::password(12);
            $user = $this->userRepository->create(array_merge($userPayload, [
                'password' => bcrypt($plainPassword),
            ]));
            $wasCreated = true;
        }

        return [$user, $plainPassword, $wasCreated];
    }

    /**
     * Send employee credentials mail.
     */
    protected function sendEmployeeCredentials($user, ?string $plainPassword): void
    {
        if (! $plainPassword) {
            return;
        }

        try {
            Mail::queue(new UserCreatedNotification($user, $plainPassword));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Resolve or create the configured software company organization for employees.
     */
    protected function resolveEmployeeOrganizationId(): ?int
    {
        $companyName = trim((string) core()->getConfigData('general.general.company_info.company_name'));

        if ($companyName === '') {
            return null;
        }

        $organization = $this->organizationRepository->findOneWhere([
            'name' => $companyName,
        ]);

        if (! $organization) {
            $organization = $this->organizationRepository->create([
                'entity_type'       => 'organizations',
                'name'              => $companyName,
                'phone'             => core()->getConfigData('general.general.company_info.telephone'),
                'website'           => core()->getConfigData('general.general.company_info.website'),
                'billing_street'    => core()->getConfigData('general.general.company_info.address'),
                'billing_city'      => null,
                'billing_state'     => null,
                'billing_postcode'  => null,
                'billing_country'   => null,
                'description'       => 'Software company',
            ]);
        } else {
            $updatePayload = [];

            if (blank($organization->phone) && core()->getConfigData('general.general.company_info.telephone')) {
                $updatePayload['phone'] = core()->getConfigData('general.general.company_info.telephone');
            }

            if (blank($organization->website) && core()->getConfigData('general.general.company_info.website')) {
                $updatePayload['website'] = core()->getConfigData('general.general.company_info.website');
            }

            if (blank($organization->billing_street) && core()->getConfigData('general.general.company_info.address')) {
                $updatePayload['billing_street'] = core()->getConfigData('general.general.company_info.address');
            }

            if (! empty($updatePayload)) {
                $updatePayload['entity_type'] = 'organizations';
                $organization = $this->organizationRepository->update($updatePayload, $organization->id);
            }
        }

        return $organization?->id;
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
        $employeeRoles = $this->getEmployeeRoles();
        $isEmployeeRoute = $routeType === 'employee';

        return view('admin::contacts.persons.create', compact('organization', 'routeType', 'employeeRoles', 'isEmployeeRoute'));
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

        if ($this->isEmployeeContext($data)) {
            $this->validateEmployeeFields($request);
            $data['organization_id'] = $this->resolveEmployeeOrganizationId();
        }

        $data = $this->normalizeNameFields($data);

        $plainPassword = null;

        $person = DB::transaction(function () use ($data, &$plainPassword) {
            if ($this->isEmployeeContext($data)) {
                [$user, $plainPassword] = $this->syncEmployeeUser($data);
                $data['user_id'] = $user->id;
            }

            return $this->personRepository->create($data);
        });

        if ($this->isEmployeeContext($data)) {
            $this->sendEmployeeCredentials($person->user, $plainPassword);
        }

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
                ->route('admin.'.$this->getRoutePrefix().'.persons.create', array_filter([
                    'organization_id' => $organizationId,
                    'type'            => $routeType,
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
        } elseif (str_contains($routeName, 'admin.employees.')) {
            return redirect()->route('admin.employees.persons.index');
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
        $person = $this->personRepository->with([
            'attribute_values',
            'organization',
            'user.role',
        ])->findOrFail($id);

        return view('admin::contacts.persons.view', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $person = $this->personRepository->findOrFail($id);

        $routeType = $this->getRouteType();
        $employeeRoles = $this->getEmployeeRoles();
        $isEmployeeRoute = ($routeType === 'employee') || ($person->type === 'employee');

        return view('admin::contacts.persons.edit', compact('person', 'routeType', 'employeeRoles', 'isEmployeeRoute'));
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

        $existingPerson = $this->personRepository->findOrFail($id);

        if ($this->isEmployeeContext(array_merge($data, ['type' => $existingPerson->type]))) {
            $this->validateEmployeeFields($request, $id);
            $data['organization_id'] = $this->resolveEmployeeOrganizationId();
        }

        $data = $this->normalizeNameFields($data);

        $plainPassword = null;

        $person = DB::transaction(function () use ($data, $id, $existingPerson, &$plainPassword) {
            if ($this->isEmployeeContext(array_merge($data, ['type' => $existingPerson->type]))) {
                [$user, $plainPassword] = $this->syncEmployeeUser($data, $existingPerson->user_id);
                $data['user_id'] = $user->id;
            }

            return $this->personRepository->update($data, $id);
        });

        if (($plainPassword !== null) && $this->isEmployeeContext(array_merge($data, ['type' => $existingPerson->type]))) {
            $this->sendEmployeeCredentials($person->user, $plainPassword);
        }

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

        if (str_contains($routeName, 'admin.employees.')) {
            return redirect()->route('admin.employees.persons.index');
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
