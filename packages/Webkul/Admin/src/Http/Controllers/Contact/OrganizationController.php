<?php

namespace Webkul\Admin\Http\Controllers\Contact;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Prettus\Repository\Criteria\RequestCriteria;
use Webkul\Admin\DataGrids\Contact\OrganizationDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\AttributeForm;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Resources\OrganizationResource;
use Webkul\Contact\Models\Industry;
use Webkul\Contact\Models\Organization as OrganizationModel;
use Webkul\Contact\Repositories\OrganizationRepository;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Models\PurchaseOrder;
use Webkul\PurchaseOrder\Models\VendorQuote;
use Webkul\Quote\Models\ProformaInvoice;
use Webkul\Quote\Models\Quote;

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
    public function index(): View|JsonResponse
    {
        // Detect organization type from current route and inject into query for DataGrid filtering
        if ($type = $this->getRouteType()) {
            if (!request()->query('type')) {
                request()->query->set('type', $type);
            }
        }

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
        $routeType = $this->getRouteType();
        $industries = Industry::query()->orderBy('name')->get();

        return view('admin::contacts.organizations.create', [
            'routeType' => $routeType,
            'industries' => $industries,
        ]);
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
            'id'                => $organization->id,
            'name'              => $organization->name,
            'phone'             => $organization->phone,
            'billing_street'    => $organization->billing_street,
            'billing_city'      => $organization->billing_city,
            'billing_state'     => $organization->billing_state,
            'billing_postcode'  => $organization->billing_postcode,
            'billing_country'   => $organization->billing_country,
            'shipping_street'   => $organization->shipping_street,
            'shipping_city'     => $organization->shipping_city,
            'shipping_state'    => $organization->shipping_state,
            'shipping_postcode' => $organization->shipping_postcode,
            'shipping_country'  => $organization->shipping_country,
        ]);
    }

    /**
     * Search customers only (for lookup/searchable dropdowns).
     */
    public function searchCustomers(): AnonymousResourceCollection
    {
        $organizations = $this->organizationRepository
            ->pushCriteria(app(RequestCriteria::class))
            ->whereIn('type', ['customer', 'Customer'])
            ->take(20)
            ->get();

        return OrganizationResource::collection($organizations);
    }

    /**
     * Create a customer/vendor from inline pickers without leaving the current form.
     */
    public function quickCreate(AttributeForm $request): JsonResponse
    {
        $routeType = $this->getRouteType();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['nullable', 'in:customer,vendor,Customer,Vendor'],
        ]);

        $type = $this->normalizeOrganizationType($data['type'] ?? $routeType) ?: 'customer';

        $organization = OrganizationModel::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])])
            ->first();

        if (! $organization) {
            $organization = $this->organizationRepository->create([
                'entity_type' => 'organizations',
                'name'        => $data['name'],
                'type'        => $type,
            ]);
        } elseif (strtolower((string) $organization->type) !== $type) {
            return response()->json([
                'message' => 'A company with this name already exists as ' . ($organization->type ?: 'another type') . '.',
            ], 422);
        }

        return response()->json([
            'id'      => $organization->id,
            'name'    => $organization->name,
            'type'    => $organization->type,
            'message' => ucfirst($type) . ' created successfully.',
        ], 201);
    }

    /**
     * Display the specified organization (detail page).
     */
    public function show(int $id): View
    {
        $organization = $this->organizationRepository->findOrFail($id);
        $documentSections = $this->buildDocumentSections($organization, $this->getRouteType());

        return view('admin::contacts.organizations.view', compact('organization', 'documentSections'));
    }

    protected function buildDocumentSections($organization, ?string $routeType): array
    {
        $normalizedType = strtolower((string) ($organization->type ?? ''));
        $entityType = $routeType ?: (in_array($normalizedType, ['customer', 'vendor']) ? $normalizedType : null);

        if ($entityType === 'vendor') {
            return [
                $this->makeDocumentSection(
                    title: 'RFQs',
                    emptyMessage: 'No RFQs found for this vendor yet.',
                    count: VendorQuote::query()->where('organization_id', $organization->id)->count(),
                    records: VendorQuote::query()->where('organization_id', $organization->id)->latest('issue_date')->latest('id')->take(3)->get(),
                    itemUrl: fn ($record) => route('admin.vendor_quotes.view', $record->id),
                    allUrl: route('admin.vendor_quotes.index', ['organization_id' => $organization->id]),
                    titleValue: fn ($record) => $record->vendor_quote_number ?: ('RFQ-' . $record->id),
                    metaValue: fn ($record) => collect([
                        $record->issue_date?->format('Y-m-d'),
                        $record->status ? Str::headline($record->status) : null,
                    ])->filter()->implode(' | ')
                ),
                $this->makeDocumentSection(
                    title: 'Purchase Orders',
                    emptyMessage: 'No purchase orders found for this vendor yet.',
                    count: PurchaseOrder::query()->where('organization_id', $organization->id)->count(),
                    records: PurchaseOrder::query()->where('organization_id', $organization->id)->latest('created_at')->take(3)->get(),
                    itemUrl: fn ($record) => route('admin.purchase_orders.view', $record->id),
                    allUrl: route('admin.purchase_orders.index', ['organization_id' => $organization->id]),
                    titleValue: fn ($record) => $record->po_number ?: ('PO-' . $record->id),
                    metaValue: fn ($record) => collect([
                        optional($record->created_at)->format('Y-m-d'),
                        $record->status ? Str::headline($record->status) : null,
                    ])->filter()->implode(' | ')
                ),
            ];
        }

        return [
            $this->makeDocumentSection(
                title: 'Quotes',
                emptyMessage: 'No quotes found for this customer yet.',
                count: Quote::query()->where('organization_id', $organization->id)->count(),
                records: Quote::query()->where('organization_id', $organization->id)->latest('quote_date')->latest('id')->take(3)->get(),
                itemUrl: fn ($record) => route('admin.quotes.view', $record->id),
                allUrl: route('admin.quotes.index', ['organization_id' => $organization->id]),
                titleValue: fn ($record) => $record->quote_number ?: ('Q' . $record->id),
                metaValue: fn ($record) => collect([
                    $record->quote_date?->format('Y-m-d'),
                    $record->status ? Str::headline($record->status) : null,
                ])->filter()->implode(' | ')
            ),
            $this->makeDocumentSection(
                title: 'Proforma Invoices',
                emptyMessage: 'No proforma invoices found for this customer yet.',
                count: ProformaInvoice::query()->where('organization_id', $organization->id)->count(),
                records: ProformaInvoice::query()->where('organization_id', $organization->id)->latest('issue_date')->latest('id')->take(3)->get(),
                itemUrl: fn ($record) => route('admin.proforma_invoices.view', $record->id),
                allUrl: route('admin.proforma_invoices.index', ['organization_id' => $organization->id]),
                titleValue: fn ($record) => $record->proforma_number ?: ('PF-' . $record->id),
                metaValue: fn ($record) => collect([
                    $record->issue_date?->format('Y-m-d'),
                    $record->status ? Str::headline($record->status) : null,
                ])->filter()->implode(' | ')
            ),
            $this->makeDocumentSection(
                title: 'Job Orders',
                emptyMessage: 'No job orders found for this customer yet.',
                count: JobOrder::query()->where('organization_id', $organization->id)->count(),
                records: JobOrder::query()->where('organization_id', $organization->id)->latest('issue_date')->latest('id')->take(3)->get(),
                itemUrl: fn ($record) => route('admin.job_orders.view', $record->id),
                allUrl: route('admin.job_orders.index', ['organization_id' => $organization->id]),
                titleValue: fn ($record) => $record->job_order_number ?: ('JO-' . $record->id),
                metaValue: fn ($record) => collect([
                    $record->issue_date?->format('Y-m-d'),
                    $record->status ? Str::headline($record->status) : null,
                ])->filter()->implode(' | ')
            ),
        ];
    }

    protected function makeDocumentSection(
        string $title,
        string $emptyMessage,
        int $count,
        $records,
        callable $itemUrl,
        string $allUrl,
        callable $titleValue,
        callable $metaValue
    ): array {
        return [
            'title' => $title,
            'empty_message' => $emptyMessage,
            'count' => $count,
            'all_url' => $allUrl,
            'records' => $records->map(fn ($record) => [
                'title' => $titleValue($record),
                'meta' => $metaValue($record),
                'url' => $itemUrl($record),
            ]),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttributeForm $request): RedirectResponse
    {
        // If on customers/vendors route, auto-set the type
        $organizationType = $request->input('organization_type', $request->input('type'));
        if (! $organizationType && $routeType = $this->getRouteType()) {
            $organizationType = $routeType;
        }

        $normalizedType = $this->normalizeOrganizationType($organizationType);

        $request->validate([
            'description'      => ['nullable', 'max:100'],
            'billing_street'   => ['nullable', 'max:100'],
            'shipping_street'  => ['nullable', 'max:100'],
            'addresses'        => ['nullable', 'array'],
            'addresses.*.type' => ['nullable', 'string', 'max:50'],
            'addresses.*.street' => ['nullable', 'string', 'max:100'],
            'addresses.*.city' => ['nullable', 'string', 'max:100'],
            'addresses.*.state' => ['nullable', 'string', 'max:100'],
            'addresses.*.postcode' => ['nullable', 'string', 'max:100'],
            'addresses.*.country' => ['nullable', 'string', 'max:100'],
            'industry'         => ['nullable', 'string', 'max:255'],
            'organization_type' => ['required_without:type', 'in:customer,vendor,Customer,Vendor'],
            'type'             => ['nullable', 'in:customer,vendor,Customer,Vendor'],
        ]);

        $data = $request->all();
        $data['type'] = $normalizedType;
        unset($data['organization_type']);

        if ($request->boolean('same_as_billing')) {
            $data['shipping_street']   = $data['billing_street']   ?? null;
            $data['shipping_city']     = $data['billing_city']     ?? null;
            $data['shipping_state']    = $data['billing_state']    ?? null;
            $data['shipping_postcode'] = $data['billing_postcode'] ?? null;
            $data['shipping_country']  = $data['billing_country']  ?? null;
        }

        $data['address'] = $this->normalizeAddressBook($data, $request->boolean('same_as_billing'));

        $this->syncPrimaryAddressFields($data, 'billing');
        $this->syncPrimaryAddressFields($data, 'shipping');

        Event::dispatch('contacts.organization.create.before');

        $organization = $this->organizationRepository->create($data);

        Event::dispatch('contacts.organization.create.after', $organization);

        session()->flash('success', trans('admin::app.contacts.organizations.index.create-success'));

        // Redirect to the appropriate list based on route
        $routeName = request()->route()?->getName() ?? '';
        if (str_contains($routeName, 'admin.customers.')) {
            return redirect()->route('admin.customers.organizations.index');
        } elseif (str_contains($routeName, 'admin.vendors.')) {
            return redirect()->route('admin.vendors.organizations.index');
        }

        return redirect()->route('admin.contacts.organizations.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $organization = $this->organizationRepository->findOrFail($id);
        $routeType = $this->getRouteType();
        $industries = Industry::query()->orderBy('name')->get();

        return view('admin::contacts.organizations.edit', compact('organization', 'routeType', 'industries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttributeForm $request, int $id): RedirectResponse
    {
        // If on customers/vendors route, auto-set the type
        $organizationType = $request->input('organization_type', $request->input('type'));
        if (! $organizationType && $routeType = $this->getRouteType()) {
            $organizationType = $routeType;
        }

        $normalizedType = $this->normalizeOrganizationType($organizationType);

        $request->validate([
            'description'      => ['nullable', 'max:100'],
            'billing_street'   => ['nullable', 'max:100'],
            'shipping_street'  => ['nullable', 'max:100'],
            'addresses'        => ['nullable', 'array'],
            'addresses.*.type' => ['nullable', 'string', 'max:50'],
            'addresses.*.street' => ['nullable', 'string', 'max:100'],
            'addresses.*.city' => ['nullable', 'string', 'max:100'],
            'addresses.*.state' => ['nullable', 'string', 'max:100'],
            'addresses.*.postcode' => ['nullable', 'string', 'max:100'],
            'addresses.*.country' => ['nullable', 'string', 'max:100'],
            'industry'         => ['nullable', 'string', 'max:255'],
            'organization_type' => ['required_without:type', 'in:customer,vendor,Customer,Vendor'],
            'type'             => ['nullable', 'in:customer,vendor,Customer,Vendor'],
        ]);

        $data = $request->all();
        $data['type'] = $normalizedType;
        unset($data['organization_type']);

        if ($request->boolean('same_as_billing')) {
            $data['shipping_street']   = $data['billing_street']   ?? null;
            $data['shipping_city']     = $data['billing_city']     ?? null;
            $data['shipping_state']    = $data['billing_state']    ?? null;
            $data['shipping_postcode'] = $data['billing_postcode'] ?? null;
            $data['shipping_country']  = $data['billing_country']  ?? null;
        }

        $data['address'] = $this->normalizeAddressBook($data, $request->boolean('same_as_billing'));

        $this->syncPrimaryAddressFields($data, 'billing');
        $this->syncPrimaryAddressFields($data, 'shipping');

        Event::dispatch('contacts.organization.update.before', $id);

        $organization = $this->organizationRepository->update($data, $id);

        Event::dispatch('contacts.organization.update.after', $organization);

        session()->flash('success', trans('admin::app.contacts.organizations.index.update-success'));

        // Redirect to the appropriate list/view based on route
        $routeName = request()->route()?->getName() ?? '';
        if (str_contains($routeName, 'admin.customers.')) {
            return redirect()->route('admin.customers.organizations.view', $id);
        } elseif (str_contains($routeName, 'admin.vendors.')) {
            return redirect()->route('admin.vendors.organizations.view', $id);
        }

        return redirect()->route('admin.contacts.organizations.view', $id);
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

    /**
     * Normalize organization type to canonical lowercase values.
     */
    protected function normalizeOrganizationType(?string $type): ?string
    {
        if (! $type) {
            return null;
        }

        $normalized = strtolower(trim($type));

        return in_array($normalized, ['customer', 'vendor']) ? $normalized : null;
    }

    /**
     * Normalize dynamic addresses to a single array payload.
     */
    protected function normalizeAddressBook(array $data, bool $sameAsBilling = false): array
    {
        $addresses = [];

        foreach (($data['addresses'] ?? []) as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $type = strtolower(trim((string) ($entry['type'] ?? 'other')));
            if (! in_array($type, ['billing', 'shipping', 'other'])) {
                $type = 'other';
            }

            $normalized = [
                'type'     => $type,
                'street'   => trim((string) ($entry['street'] ?? '')),
                'city'     => trim((string) ($entry['city'] ?? '')),
                'state'    => trim((string) ($entry['state'] ?? '')),
                'postcode' => trim((string) ($entry['postcode'] ?? '')),
                'country'  => trim((string) ($entry['country'] ?? '')),
            ];

            if (collect($normalized)->except('type')->filter()->isEmpty()) {
                continue;
            }

            $addresses[] = $normalized;
        }

        $primaryBilling = $this->buildPrimaryAddressPayload($data, 'billing');
        $primaryShipping = $sameAsBilling
            ? ($primaryBilling ? array_merge($primaryBilling, ['type' => 'shipping']) : null)
            : $this->buildPrimaryAddressPayload($data, 'shipping');

        if ($primaryBilling) {
            $addresses = collect($addresses)
                ->reject(fn (array $address) => $address['type'] === 'billing' && $this->addressesMatch($address, $primaryBilling))
                ->values()
                ->all();

            array_unshift($addresses, $primaryBilling);
        }

        if ($primaryShipping) {
            $addresses = collect($addresses)
                ->reject(fn (array $address) => $address['type'] === 'shipping' && $this->addressesMatch($address, $primaryShipping))
                ->values()
                ->all();

            $insertAt = $primaryBilling ? 1 : 0;
            array_splice($addresses, $insertAt, 0, [$primaryShipping]);
        }

        return array_values($addresses);
    }

    /**
     * Build normalized primary billing/shipping address from legacy columns.
     */
    protected function buildPrimaryAddressPayload(array $data, string $type): ?array
    {
        $address = [
            'type'     => $type,
            'street'   => trim((string) ($data["{$type}_street"] ?? '')),
            'city'     => trim((string) ($data["{$type}_city"] ?? '')),
            'state'    => trim((string) ($data["{$type}_state"] ?? '')),
            'postcode' => trim((string) ($data["{$type}_postcode"] ?? '')),
            'country'  => trim((string) ($data["{$type}_country"] ?? '')),
        ];

        return collect($address)->except('type')->filter()->isEmpty() ? null : $address;
    }

    /**
     * Compare two normalized address payloads.
     */
    protected function addressesMatch(array $left, array $right): bool
    {
        return ($left['street'] ?? '') === ($right['street'] ?? '')
            && ($left['city'] ?? '') === ($right['city'] ?? '')
            && ($left['state'] ?? '') === ($right['state'] ?? '')
            && ($left['postcode'] ?? '') === ($right['postcode'] ?? '')
            && ($left['country'] ?? '') === ($right['country'] ?? '');
    }

    /**
     * Backward compatibility: map first typed address into legacy columns.
     */
    protected function syncPrimaryAddressFields(array &$data, string $type): void
    {
        $address = collect($data['address'] ?? [])->firstWhere('type', $type);

        if (! $address) {
            return;
        }

        $data["{$type}_street"] = $address['street'] ?: null;
        $data["{$type}_city"] = $address['city'] ?: null;
        $data["{$type}_state"] = $address['state'] ?: null;
        $data["{$type}_postcode"] = $address['postcode'] ?: null;
        $data["{$type}_country"] = $address['country'] ?: null;
    }
}
