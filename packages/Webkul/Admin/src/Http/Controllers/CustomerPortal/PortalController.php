<?php

namespace Webkul\Admin\Http\Controllers\CustomerPortal;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Services\CustomerPortal\OrderProgressPresenter;
use Webkul\Contact\Models\Organization;
use Webkul\Contact\Models\Person;
use Webkul\Product\Models\Product;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\Quote\Models\ProformaInvoice;
use Webkul\Quote\Models\Quote;
use Webkul\Quote\Models\Invoice;

class PortalController extends Controller
{
    protected function organization(): Organization
    {
        return Auth::guard('customer')->user()->organization;
    }

    public function dashboard(OrderProgressPresenter $presenter): View
    {
        $organization = $this->organization();
        $portalUser = Auth::guard('customer')->user();
        $canViewDocuments = $portalUser->hasPortalPermission('view_documents');
        $canViewProducts = $portalUser->hasPortalPermission('view_products');
        $id = $organization->id;
        $quotes = Quote::visibleToCustomer($id);
        $proformas = ProformaInvoice::visibleToCustomer($id);
        $invoices = Invoice::visibleToCustomer($id);
        $orders = JobOrder::visibleToCustomer($id);
        $stats = [
            'quotes'      => $canViewDocuments ? (clone $quotes)->count() : 0,
            'proformas'   => $canViewDocuments ? (clone $proformas)->count() : 0,
            'invoices'    => $canViewDocuments ? (clone $invoices)->count() : 0,
            'jobOrders'   => $canViewDocuments ? (clone $orders)->whereNotIn('status', ['completed', 'closed', 'cancelled'])->count() : 0,
            'products'    => $canViewProducts ? Product::where('customer_organization_id', $id)->count() : 0,
            'deliveries'  => $canViewDocuments ? (clone $orders)->whereNotNull('required_delivery_date')->whereDate('required_delivery_date', '>=', today())->count() : 0,
            'outstanding' => $canViewDocuments ? (string) ((clone $invoices)->sum('remaining_amount') + (clone $proformas)->whereNull('converted_to_invoice_id')->sum('remaining_amount')) : '0',
        ];

        $recentQuotes = $canViewDocuments ? (clone $quotes)->latest('quote_date')->latest('id')->take(5)->get() : collect();
        $recentProformas = $canViewDocuments ? (clone $proformas)->latest('issue_date')->latest('id')->take(5)->get() : collect();
        $recentInvoices = $canViewDocuments ? (clone $invoices)->latest('issue_date')->latest('id')->take(5)->get() : collect();
        $recentJobOrders = $canViewDocuments ? (clone $orders)->latest('issue_date')->latest('id')->take(5)->get() : collect();
        $activeOrders = $canViewDocuments
            ? (clone $orders)->whereNotIn('status', ['completed', 'closed', 'cancelled'])
                ->orderByRaw('required_delivery_date IS NULL, required_delivery_date')
                ->take(4)
                ->get()
                ->map(fn ($order) => ['record' => $order, 'progress' => $presenter->present($order->status)])
            : collect();

        return view('admin::customer-portal.dashboard', compact('organization', 'stats', 'recentQuotes', 'recentProformas', 'recentInvoices', 'recentJobOrders', 'activeOrders', 'canViewDocuments', 'canViewProducts'));
    }

    public function company(): View
    {
        return view('admin::customer-portal.company', ['organization' => $this->organization()]);
    }

    public function contacts(): View
    {
        $this->requirePermission('view_contacts');
        $organization = $this->organization();
        $records = Person::query()->where('organization_id', $organization->id)
            ->when(request('q'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('job_title', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('cell_phone', 'like', '%'.$search.'%');
                });
            })
            ->select(['id', 'organization_id', 'name', 'job_title', 'email', 'phone', 'cell_phone'])
            ->orderBy('name')->paginate($this->perPage())->withQueryString();

        return view('admin::customer-portal.contacts', compact('organization', 'records'));
    }

    public function contact(int $id): View
    {
        $this->requirePermission('view_contacts');
        $organization = $this->organization();
        $contact = Person::query()
            ->where('organization_id', $organization->id)
            ->findOrFail($id);
        $recentQuotes = Quote::visibleToCustomer($organization->id)
            ->where('person_id', $contact->id)
            ->latest('quote_date')
            ->take(5)
            ->get();
        $recentProformas = ProformaInvoice::visibleToCustomer($organization->id)
            ->where('person_id', $contact->id)
            ->latest('issue_date')
            ->take(5)
            ->get();

        return view('admin::customer-portal.contacts.view', compact('organization', 'contact', 'recentQuotes', 'recentProformas'));
    }

    public function quotes(): View
    {
        $this->requirePermission('view_documents');
        $organization = $this->organization();
        $records = Quote::visibleToCustomer($organization->id)
            ->when(request('q'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('quote_number', 'like', '%'.$search.'%')
                        ->orWhere('subject', 'like', '%'.$search.'%');
                });
            })
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest('quote_date')->latest('id')->paginate($this->perPage())->withQueryString();

        return view('admin::customer-portal.quotes.index', compact('organization', 'records'));
    }

    public function quote(int $id): View
    {
        $this->requirePermission('view_documents');
        $organization = $this->organization();
        $quote = Quote::visibleToCustomer($organization->id)
            ->with(['items', 'user', 'person', 'organization', 'additionalCharges'])
            ->findOrFail($id);

        return view('admin::customer-portal.quotes.view', compact('organization', 'quote'));
    }

    public function quotePdf(int $id): Response|StreamedResponse
    {
        $this->requirePermission('view_documents');
        $quote = Quote::visibleToCustomer($this->organization()->id)
            ->with(['organization', 'user', 'items', 'additionalCharges'])->findOrFail($id);

        return $this->downloadPDF(view('admin::quotes.pdf', compact('quote'))->render(), 'Quote_'.$quote->quote_number);
    }

    public function quoteAttachment(int $id): BinaryFileResponse
    {
        $this->requirePermission('view_documents');
        $quote = Quote::visibleToCustomer($this->organization()->id)->findOrFail($id);

        abort_unless($quote->attachment_path && Storage::disk('public')->exists($quote->attachment_path), 404);

        return response()->download(Storage::disk('public')->path($quote->attachment_path), basename($quote->attachment_path));
    }

    public function proformas(): View
    {
        $this->requirePermission('view_documents');
        $organization = $this->organization();
        $records = ProformaInvoice::visibleToCustomer($organization->id)
            ->when(request('q'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('proforma_number', 'like', '%'.$search.'%')
                        ->orWhere('subject', 'like', '%'.$search.'%')
                        ->orWhere('customer_po_reference', 'like', '%'.$search.'%');
                });
            })
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest('issue_date')->latest('id')->paginate($this->perPage())->withQueryString();

        return view('admin::customer-portal.proformas.index', compact('organization', 'records'));
    }

    public function proforma(int $id): View
    {
        $this->requirePermission('view_documents');
        $organization = $this->organization();
        $proforma = ProformaInvoice::visibleToCustomer($organization->id)
            ->with(['items', 'receipts', 'quote', 'person', 'salesOwner', 'organization', 'additionalCharges'])
            ->findOrFail($id);

        return view('admin::customer-portal.proformas.view', compact('organization', 'proforma'));
    }

    public function proformaPdf(int $id): Response|StreamedResponse
    {
        $this->requirePermission('view_documents');
        $proformaInvoice = ProformaInvoice::visibleToCustomer($this->organization()->id)
            ->with(['organization', 'salesOwner', 'quote', 'items', 'additionalCharges'])->findOrFail($id);

        return $this->downloadPDF(view('admin::proforma-invoices.pdf', compact('proformaInvoice'))->render(), 'Proforma_'.$proformaInvoice->proforma_number);
    }

    public function proformaAttachment(int $id): BinaryFileResponse
    {
        $this->requirePermission('view_documents');
        $record = ProformaInvoice::visibleToCustomer($this->organization()->id)->findOrFail($id);
        abort_unless($record->attachment_path && Storage::disk('public')->exists($record->attachment_path), 404);

        return response()->download(Storage::disk('public')->path($record->attachment_path), basename($record->attachment_path));
    }

    public function invoices(): View
    {
        $this->requirePermission('view_documents');
        $organization = $this->organization();
        $records = Invoice::visibleToCustomer($organization->id)
            ->when(request('q'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', '%'.$search.'%')
                        ->orWhere('subject', 'like', '%'.$search.'%')
                        ->orWhere('customer_po_reference', 'like', '%'.$search.'%');
                });
            })
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest('issue_date')->latest('id')->paginate($this->perPage())->withQueryString();

        return view('admin::customer-portal.invoices.index', compact('organization', 'records'));
    }

    public function invoice(int $id): View
    {
        $this->requirePermission('view_documents');
        $organization = $this->organization();
        $invoice = Invoice::visibleToCustomer($organization->id)
            ->with(['items', 'receipts', 'proformaInvoice.receipts', 'organization', 'salesOwner', 'additionalCharges'])
            ->findOrFail($id);

        return view('admin::customer-portal.invoices.view', compact('organization', 'invoice'));
    }

    public function invoicePdf(int $id): Response|StreamedResponse
    {
        $this->requirePermission('view_documents');
        $invoice = Invoice::visibleToCustomer($this->organization()->id)
            ->with(['items', 'receipts', 'proformaInvoice', 'quote', 'organization', 'salesOwner', 'additionalCharges'])
            ->findOrFail($id);

        return $this->downloadPDF(view('admin::invoices.pdf', compact('invoice'))->render(), 'Invoice_'.$invoice->invoice_number);
    }

    public function jobOrders(): View
    {
        $this->requirePermission('view_documents');
        $organization = $this->organization();
        $records = JobOrder::visibleToCustomer($organization->id)
            ->when(request('q'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('job_order_number', 'like', '%'.$search.'%')
                        ->orWhere('subject', 'like', '%'.$search.'%')
                        ->orWhere('customer_po_reference', 'like', '%'.$search.'%');
                });
            })
            ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest('issue_date')->latest('id')->paginate($this->perPage())->withQueryString();

        return view('admin::customer-portal.job-orders.index', compact('organization', 'records'));
    }

    public function jobOrder(int $id, OrderProgressPresenter $presenter): View
    {
        $this->requirePermission('view_documents');
        $organization = $this->organization();
        $jobOrder = JobOrder::visibleToCustomer($organization->id)
            ->with(['items.product', 'items.proformaInvoiceItem'])
            ->findOrFail($id);
        $progress = $presenter->present($jobOrder->status);

        return view('admin::customer-portal.job-orders.view', compact('organization', 'jobOrder', 'progress'));
    }

    public function products(): View
    {
        $this->requirePermission('view_products');
        $organization = $this->organization();
        $records = Product::query()->with(['colors', 'otherImages.color'])
            ->where('customer_organization_id', $organization->id)
            ->when(request('q'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->select(['id', 'customer_organization_id', 'name', 'sku', 'description', 'size', 'cover_image'])
            ->latest('id')->paginate($this->perPage())->withQueryString();

        return view('admin::customer-portal.products.index', compact('organization', 'records'));
    }

    public function product(int $id): View
    {
        $this->requirePermission('view_products');
        $organization = $this->organization();
        $product = Product::query()->with(['colors', 'otherImages.color', 'keyPoints'])
            ->where('customer_organization_id', $organization->id)
            ->select(['id', 'customer_organization_id', 'name', 'sku', 'description', 'size', 'style', 'weight', 'weight_unit', 'cover_image', 'additional_info', 'shipping_info'])
            ->findOrFail($id);

        return view('admin::customer-portal.products.view', compact('organization', 'product'));
    }

    public function security(): View
    {
        return view('admin::customer-portal.security', ['organization' => $this->organization(), 'portalUser' => Auth::guard('customer')->user()]);
    }

    public function updateSecurity(Request $request): RedirectResponse
    {
        $user = Auth::guard('customer')->user();
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'current_password' => ['required_with:password', 'current_password:customer'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        $user->name = $data['name'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
            $user->must_change_password = false;
        }
        $user->save();

        return back()->with('success', 'Profile and security settings updated.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.session.create');
    }

    public static function money($value): string
    {
        return core()->currencySymbol(config('app.currency')).' '.static::decimal($value, 2);
    }

    public static function decimal($value, int $places = 4): string
    {
        $value = preg_replace('/[^0-9.\-]/', '', (string) ($value ?? '0')) ?: '0';
        $negative = str_starts_with($value, '-');
        $value = ltrim($value, '-');
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        $digits = str_pad($fraction, $places + 1, '0');
        $fraction = substr($digits, 0, $places);

        if ((int) ($digits[$places] ?? 0) >= 5) {
            $scaled = (int) ($whole ?: '0') * (10 ** $places) + (int) ($fraction ?: '0') + 1;
            $whole = (string) intdiv($scaled, 10 ** $places);
            $fraction = str_pad((string) ($scaled % (10 ** $places)), $places, '0', STR_PAD_LEFT);
        }

        $formatted = number_format((int) ($whole ?: '0'), 0, '.', ',').($places ? '.'.$fraction : '');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $negative && $formatted !== '0' ? '-'.$formatted : $formatted;
    }

    public static function productImageUrl(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    protected function perPage(): int
    {
        $perPage = (int) request('per_page', 10);

        return in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 10;
    }

    protected function requirePermission(string $permission): void
    {
        abort_unless(Auth::guard('customer')->user()->hasPortalPermission($permission), 403);
    }
}
