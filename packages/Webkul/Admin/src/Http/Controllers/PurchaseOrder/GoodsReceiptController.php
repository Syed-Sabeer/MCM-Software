<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\PurchaseOrder\GoodsReceiptDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\GoodsReceiptRequest;
use Webkul\PurchaseOrder\Models\GoodsReceipt;
use Webkul\PurchaseOrder\Repositories\GoodsReceiptRepository;
use Webkul\PurchaseOrder\Repositories\PurchaseOrderRepository;

class GoodsReceiptController extends Controller
{
    public function __construct(
        protected GoodsReceiptRepository $goodsReceiptRepository,
        protected PurchaseOrderRepository $purchaseOrderRepository
    ) {
    }

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(GoodsReceiptDataGrid::class)->process();
        }

        return view('admin::goods-receipts.index');
    }

    public function create(): View
    {
        $purchaseOrder = $this->purchaseOrderRepository->with(['organization', 'items'])->findOrFail(request('purchase_order_id'));
        $nextGoodsReceiptNumber = GoodsReceipt::generateNextNumber();

        return view('admin::goods-receipts.create', compact('purchaseOrder', 'nextGoodsReceiptNumber'));
    }

    public function store(GoodsReceiptRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $payload['received_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('goods-receipts', 'public');
        }

        $goodsReceipt = $this->goodsReceiptRepository->create($payload);

        session()->flash('success', 'Goods receipt posted successfully.');

        return redirect()->route('admin.goods_receipts.view', $goodsReceipt->id);
    }

    public function view(int $id): View
    {
        $goodsReceipt = $this->goodsReceiptRepository->with(['vendor', 'receiver', 'purchaseOrder.organization', 'items'])->findOrFail($id);

        return view('admin::goods-receipts.view', compact('goodsReceipt'));
    }
}
