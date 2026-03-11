<?php

namespace Webkul\Admin\Http\Controllers\PurchaseOrder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
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
        Event::dispatch('goods_receipt.create.before');

        $payload = $request->validated();
        $payload['received_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $payload['attachment_path'] = $request->file('attachment')->store('goods-receipts', 'public');
        }

        $goodsReceipt = $this->goodsReceiptRepository->create($payload);

        Event::dispatch('goods_receipt.create.after', $goodsReceipt);

        session()->flash('success', 'Goods receipt posted successfully.');

        return redirect()->route('admin.goods_receipts.view', $goodsReceipt->id);
    }

    public function edit(int $id): View
    {
        $goodsReceipt = $this->goodsReceiptRepository->with(['items', 'purchaseOrder.organization', 'purchaseOrder.items'])->findOrFail($id);
        $purchaseOrder = $goodsReceipt->purchaseOrder;

        return view('admin::goods-receipts.edit', compact('goodsReceipt', 'purchaseOrder'));
    }

    public function update(GoodsReceiptRequest $request, int $id): RedirectResponse
    {
        Event::dispatch('goods_receipt.update.before', $id);

        $existing = $this->goodsReceiptRepository->findOrFail($id);
        $payload = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($existing->attachment_path) {
                Storage::disk('public')->delete($existing->attachment_path);
            }

            $payload['attachment_path'] = $request->file('attachment')->store('goods-receipts', 'public');
        } else {
            $payload['attachment_path'] = $existing->attachment_path;
        }

        $goodsReceipt = $this->goodsReceiptRepository->update($payload, $id);

        Event::dispatch('goods_receipt.update.after', $goodsReceipt);

        session()->flash('success', 'Goods receipt updated successfully.');

        return redirect()->route('admin.goods_receipts.view', $goodsReceipt->id);
    }

    public function view(int $id): View
    {
        $goodsReceipt = $this->goodsReceiptRepository->with(['vendor', 'receiver', 'purchaseOrder.organization', 'items'])->findOrFail($id);

        return view('admin::goods-receipts.view', compact('goodsReceipt'));
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            Event::dispatch('goods_receipt.delete.before', $id);

            $goodsReceipt = $this->goodsReceiptRepository->findOrFail($id);

            if ($goodsReceipt->attachment_path) {
                Storage::disk('public')->delete($goodsReceipt->attachment_path);
            }

            $this->goodsReceiptRepository->delete($id);

            Event::dispatch('goods_receipt.delete.after', $id);

            return response()->json(['message' => 'Goods receipt deleted successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Goods receipt cannot be deleted.'], 400);
        }
    }
}
