<?php

namespace Webkul\Admin\Http\Controllers\Search;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Menu\MenuItem;
use Webkul\Core\Repositories\CoreConfigRepository;
use Webkul\Contact\Models\Organization;
use Webkul\Contact\Models\Person;
use Webkul\Lead\Models\Lead;
use Webkul\Product\Models\Product;
use Webkul\PurchaseOrder\Models\PurchaseOrder;
use Webkul\Quote\Models\ProformaInvoice;
use Webkul\Quote\Models\Quote;

class GlobalSearchController extends Controller
{
    public function __construct(protected CoreConfigRepository $configurationRepository) {}

    public function search(): JsonResponse
    {
        $query = trim((string) request()->query('query', ''));
        $limit = max(1, min((int) request()->query('limit', 5), 10));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'data' => $this->emptyResults(),
            ]);
        }

        $results = [
            'organizations'      => $this->searchOrganizations($query, $limit),
            'persons'            => $this->searchPersons($query, $limit),
            'leads'              => $this->searchLeads($query, $limit),
            'products'           => $this->searchProducts($query, $limit),
            'quotes'             => $this->searchQuotes($query, $limit),
            'proforma_invoices'  => $this->searchProformas($query, $limit),
            'purchase_orders'    => $this->searchPurchaseOrders($query, $limit),
            'settings'           => $this->searchSettings($query, $limit),
            'configurations'     => $this->searchConfigurations($query, $limit),
        ];

        $results['all'] = collect([
            ...$results['organizations']->take(4)->all(),
            ...$results['persons']->take(4)->all(),
            ...$results['leads']->take(4)->all(),
            ...$results['products']->take(4)->all(),
            ...$results['quotes']->take(4)->all(),
            ...$results['proforma_invoices']->take(4)->all(),
            ...$results['purchase_orders']->take(4)->all(),
            ...$results['settings']->take(3)->all(),
            ...$results['configurations']->take(3)->all(),
        ])->values();

        return response()->json([
            'data' => collect($results)->map(fn ($items) => $items instanceof Collection ? $items->values() : collect($items)->values()),
        ]);
    }

    protected function emptyResults(): array
    {
        return [
            'all'               => [],
            'organizations'     => [],
            'persons'           => [],
            'leads'             => [],
            'products'          => [],
            'quotes'            => [],
            'proforma_invoices' => [],
            'purchase_orders'   => [],
            'settings'          => [],
            'configurations'    => [],
        ];
    }

    protected function searchOrganizations(string $query, int $limit): Collection
    {
        return Organization::query()
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('website', 'like', "%{$query}%")
                    ->orWhere('billing_city', 'like', "%{$query}%")
                    ->orWhere('shipping_city', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function (Organization $organization) {
                $type = strtolower(trim((string) $organization->type));

                return [
                    'id'       => $organization->id,
                    'title'    => $organization->name,
                    'subtitle' => collect([
                        $type !== '' ? ucfirst($type) : 'Company',
                        $organization->phone,
                    ])->filter()->implode(' • '),
                    'meta'     => $organization->website ?: collect([
                        $organization->billing_city,
                        $organization->billing_country,
                    ])->filter()->implode(', '),
                    'url'      => route($this->getOrganizationRouteName($type), $organization->id),
                    'section'  => 'organizations',
                    'badge'    => 'Company',
                ];
            });
    }

    protected function searchPersons(string $query, int $limit): Collection
    {
        return Person::query()
            ->with('organization')
            ->where(function ($builder) use ($query) {
                $builder->whereRaw("LOWER(TRIM(CONCAT_WS(' ', first_name, last_name))) LIKE ?", ['%' . mb_strtolower($query) . '%'])
                    ->orWhere('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('job_title', 'like', "%{$query}%");
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit($limit)
            ->get()
            ->map(function (Person $person) {
                $type = strtolower(trim((string) $person->type));
                $email = $person->email ?: collect($person->emails ?? [])->pluck('value')->filter()->first();

                return [
                    'id'       => $person->id,
                    'title'    => trim((string) ($person->name ?: trim(($person->first_name ?? '') . ' ' . ($person->last_name ?? '')))),
                    'subtitle' => collect([
                        $person->organization?->name,
                        $email,
                    ])->filter()->implode(' • '),
                    'meta'     => $person->job_title ?: ($type !== '' ? ucfirst($type) : 'Contact'),
                    'url'      => route($this->getPersonRouteName($type), $person->id),
                    'section'  => 'persons',
                    'badge'    => 'Contact',
                ];
            });
    }

    protected function searchLeads(string $query, int $limit): Collection
    {
        $leadQuery = Lead::query()
            ->with(['person.organization', 'stage'])
            ->where(function ($builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('case_no', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });

        if ($userIds = bouncer()->getAuthorizedUserIds()) {
            $leadQuery->whereIn('user_id', $userIds);
        }

        return $leadQuery
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(function (Lead $lead) {
                return [
                    'id'       => $lead->id,
                    'title'    => $lead->title ?: ('Case #' . $lead->id),
                    'subtitle' => collect([
                        $lead->case_no,
                        $lead->person?->organization?->name ?: $lead->person?->name,
                    ])->filter()->implode(' • '),
                    'meta'     => $lead->stage?->name ?: 'Case',
                    'url'      => route('admin.leads.view', $lead->id),
                    'section'  => 'leads',
                    'badge'    => 'Case',
                ];
            });
    }

    protected function searchProducts(string $query, int $limit): Collection
    {
        return Product::query()
            ->where(function ($builder) use ($query) {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('internal_code', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(function (Product $product) {
                return [
                    'id'       => $product->id,
                    'title'    => $product->name,
                    'subtitle' => collect([
                        $product->sku,
                        $product->internal_code,
                    ])->filter()->implode(' • '),
                    'meta'     => $product->selling_price !== null || $product->price !== null
                        ? 'Price: ' . number_format((float) ($product->selling_price ?? $product->price), 2)
                        : 'Product',
                    'url'      => route('admin.products.view', $product->id),
                    'section'  => 'products',
                    'badge'    => 'Product',
                ];
            });
    }

    protected function searchQuotes(string $query, int $limit): Collection
    {
        return Quote::query()
            ->with(['organization', 'person'])
            ->where(function ($builder) use ($query) {
                $builder->where('subject', 'like', "%{$query}%")
                    ->orWhere('quote_number', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(function (Quote $quote) {
                return [
                    'id'       => $quote->id,
                    'title'    => $quote->subject ?: ($quote->quote_number ?: ('Quote #' . $quote->id)),
                    'subtitle' => collect([
                        $quote->quote_number,
                        $quote->organization?->name ?: $quote->person?->name,
                    ])->filter()->implode(' • '),
                    'meta'     => ucfirst((string) $quote->status),
                    'url'      => route('admin.quotes.view', $quote->id),
                    'section'  => 'quotes',
                    'badge'    => 'Quote',
                ];
            });
    }

    protected function searchProformas(string $query, int $limit): Collection
    {
        return ProformaInvoice::query()
            ->with(['organization', 'person'])
            ->where(function ($builder) use ($query) {
                $builder->where('subject', 'like', "%{$query}%")
                    ->orWhere('proforma_number', 'like', "%{$query}%")
                    ->orWhere('customer_po_reference', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(function (ProformaInvoice $invoice) {
                return [
                    'id'       => $invoice->id,
                    'title'    => $invoice->subject ?: ($invoice->proforma_number ?: ('Proforma #' . $invoice->id)),
                    'subtitle' => collect([
                        $invoice->proforma_number,
                        $invoice->organization?->name ?: $invoice->person?->name,
                    ])->filter()->implode(' • '),
                    'meta'     => ucfirst(str_replace('_', ' ', (string) $invoice->status)),
                    'url'      => route('admin.proforma_invoices.view', $invoice->id),
                    'section'  => 'proforma_invoices',
                    'badge'    => 'Proforma',
                ];
            });
    }

    protected function searchPurchaseOrders(string $query, int $limit): Collection
    {
        return PurchaseOrder::query()
            ->with('organization')
            ->where(function ($builder) use ($query) {
                $builder->where('po_number', 'like', "%{$query}%")
                    ->orWhere('job_number', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('notes', 'like', "%{$query}%");
            })
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(function (PurchaseOrder $purchaseOrder) {
                return [
                    'id'       => $purchaseOrder->id,
                    'title'    => $purchaseOrder->po_number ?: ('PO #' . $purchaseOrder->id),
                    'subtitle' => collect([
                        $purchaseOrder->organization?->name,
                        $purchaseOrder->job_number,
                    ])->filter()->implode(' • '),
                    'meta'     => ucfirst(str_replace('_', ' ', (string) $purchaseOrder->status)),
                    'url'      => route('admin.purchase_orders.view', $purchaseOrder->id),
                    'section'  => 'purchase_orders',
                    'badge'    => 'PO',
                ];
            });
    }

    protected function searchSettings(string $query, int $limit): Collection
    {
        return $this->searchMenuItems($this->getSettingsConfig(), mb_strtolower($query))
            ->take($limit)
            ->map(fn (array $item) => [
                'id'       => $item['key'],
                'title'    => $item['name'],
                'subtitle' => 'Settings',
                'meta'     => $item['url'],
                'url'      => $item['url'],
                'section'  => 'settings',
                'badge'    => 'Setting',
            ]);
    }

    protected function searchConfigurations(string $query, int $limit): Collection
    {
        return collect($this->configurationRepository->search(
            system_config()->getItems(),
            $query
        ))->take($limit)->map(fn (array $item) => [
            'id'       => $item['key'] ?? ($item['title'] ?? uniqid('config_', true)),
            'title'    => $item['title'] ?? 'Configuration',
            'subtitle' => 'Configuration',
            'meta'     => $item['url'] ?? '',
            'url'      => $item['url'] ?? route('admin.configuration.index'),
            'section'  => 'configurations',
            'badge'    => 'Config',
        ]);
    }

    protected function searchMenuItems(Collection $menuItems, string $query): Collection
    {
        $results = collect();

        foreach ($menuItems as $item) {
            $haystack = mb_strtolower(implode(' ', array_filter([
                $item->getName(),
                $item->getKey(),
                $item->getUrl(),
            ])));

            if ($haystack !== '' && str_contains($haystack, $query)) {
                $results->push([
                    'name' => $item->getName(),
                    'url'  => $item->getUrl(),
                    'icon' => $item->getIcon(),
                    'key'  => $item->getKey(),
                ]);
            }

            if ($item->haveChildren()) {
                $results = $results->merge($this->searchMenuItems($item->getChildren(), $query));
            }
        }

        return $results->unique('url')->values();
    }

    protected function getSettingsConfig(): Collection
    {
        return menu()
            ->getItems('admin')
            ->filter(fn (MenuItem $item) => $item->getKey() === 'settings');
    }

    protected function getOrganizationRouteName(string $type): string
    {
        return match ($type) {
            'customer' => 'admin.customers.organizations.view',
            'vendor', 'vendors' => 'admin.vendors.organizations.view',
            default => 'admin.contacts.organizations.view',
        };
    }

    protected function getPersonRouteName(string $type): string
    {
        return match ($type) {
            'customer' => 'admin.customers.persons.view',
            'vendor', 'vendors' => 'admin.vendors.persons.view',
            'employee' => 'admin.employees.persons.view',
            default => 'admin.contacts.persons.view',
        };
    }
}
