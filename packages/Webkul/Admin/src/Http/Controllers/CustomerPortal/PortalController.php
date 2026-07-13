<?php

namespace Webkul\Admin\Http\Controllers\CustomerPortal;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Contact\Models\Organization;
use Webkul\Product\Models\Product;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\Quote\Models\ProformaInvoice;
use Webkul\Quote\Models\Quote;

class PortalController extends Controller
{
    protected function organization(): ?Organization
    {
        return Organization::query()
            ->where('user_id', auth()->guard('user')->id())
            ->whereIn('type', ['customer', 'Customer'])
            ->first();
    }

    public function dashboard(): View
    {
        $organization = $this->organization();

        $stats = $organization ? [
            'quotes'    => Quote::query()->where('organization_id', $organization->id)->count(),
            'proformas' => ProformaInvoice::query()->where('organization_id', $organization->id)->count(),
            'jobOrders' => JobOrder::query()->where('organization_id', $organization->id)->count(),
            'products'  => Product::query()->where('customer_organization_id', $organization->id)->count(),
        ] : ['quotes' => 0, 'proformas' => 0, 'jobOrders' => 0, 'products' => 0];

        $recentQuotes = $organization
            ? Quote::query()->where('organization_id', $organization->id)->latest('quote_date')->latest('id')->take(5)->get()
            : collect();

        $recentProformas = $organization
            ? ProformaInvoice::query()->where('organization_id', $organization->id)->latest('issue_date')->latest('id')->take(5)->get()
            : collect();

        $recentJobOrders = $organization
            ? JobOrder::query()->where('organization_id', $organization->id)->latest('issue_date')->latest('id')->take(5)->get()
            : collect();

        return view('admin::customer-portal.dashboard', compact('organization', 'stats', 'recentQuotes', 'recentProformas', 'recentJobOrders'));
    }

    public function quotes(): View
    {
        $organization = $this->organization();

        $records = $organization
            ? Quote::query()->where('organization_id', $organization->id)->latest('quote_date')->latest('id')->paginate(15)
            : collect();

        return view('admin::customer-portal.quotes.index', compact('organization', 'records'));
    }

    public function quote(int $id): View
    {
        $organization = $this->requireOrganization();

        $quote = Quote::query()
            ->with(['items', 'proformaInvoices'])
            ->where('organization_id', $organization->id)
            ->findOrFail($id);

        return view('admin::customer-portal.quotes.view', compact('organization', 'quote'));
    }

    public function proformas(): View
    {
        $organization = $this->organization();

        $records = $organization
            ? ProformaInvoice::query()->where('organization_id', $organization->id)->latest('issue_date')->latest('id')->paginate(15)
            : collect();

        return view('admin::customer-portal.proformas.index', compact('organization', 'records'));
    }

    public function proforma(int $id): View
    {
        $organization = $this->requireOrganization();

        $proforma = ProformaInvoice::query()
            ->with(['items', 'receipts', 'quote'])
            ->where('organization_id', $organization->id)
            ->findOrFail($id);

        return view('admin::customer-portal.proformas.view', compact('organization', 'proforma'));
    }

    public function jobOrders(): View
    {
        $organization = $this->organization();

        $records = $organization
            ? JobOrder::query()->where('organization_id', $organization->id)->latest('issue_date')->latest('id')->paginate(15)
            : collect();

        return view('admin::customer-portal.job-orders.index', compact('organization', 'records'));
    }

    public function jobOrder(int $id): View
    {
        $organization = $this->requireOrganization();

        $jobOrder = JobOrder::query()
            ->with(['items', 'proformaInvoice'])
            ->where('organization_id', $organization->id)
            ->findOrFail($id);

        return view('admin::customer-portal.job-orders.view', compact('organization', 'jobOrder'));
    }

    public function products(): View
    {
        $organization = $this->organization();

        $records = $organization
            ? Product::query()->where('customer_organization_id', $organization->id)->latest('id')->paginate(15)
            : collect();

        return view('admin::customer-portal.products.index', compact('organization', 'records'));
    }

    public function product(int $id): View
    {
        $organization = $this->requireOrganization();

        $product = Product::query()
            ->with(['colors', 'otherImages', 'consumptions'])
            ->where('customer_organization_id', $organization->id)
            ->findOrFail($id);

        return view('admin::customer-portal.products.view', compact('organization', 'product'));
    }

    public function logout(): RedirectResponse
    {
        auth()->guard('user')->logout();

        return redirect()->route('admin.session.create');
    }

    protected function requireOrganization(): Organization
    {
        $organization = $this->organization();

        abort_if(! $organization, 404, 'No customer company is linked to this portal user.');

        return $organization;
    }

    public static function money($value): string
    {
        return 'PKR '.number_format((float) $value, 2);
    }

    public static function productImageUrl(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }
}
