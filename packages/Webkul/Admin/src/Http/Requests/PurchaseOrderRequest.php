<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'po_number' => ['required', 'unique:purchase_orders,po_number,' . $id],
            'organization_id' => [
                'required',
                'exists:organizations,id',
                function ($attribute, $value, $fail) {
                    $type = DB::table('organizations')->where('id', $value)->value('type');
                    if (! in_array($type, ['vendor', 'Vendor'], true)) {
                        $fail('Selected organization must be a vendor.');
                    }
                },
            ],
            'person_id' => ['nullable', 'exists:persons,id'],
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
            'vendor_quote_id' => ['nullable', 'exists:vendor_quotes,id'],
            'completion_date' => ['nullable', 'date'],
            'last_delivery_date' => ['nullable', 'date'],
            'expected_receive_date' => ['nullable', 'date'],
            'payment_term' => ['nullable', 'string', 'max:255'],
            'shipping_method' => ['nullable', 'string', 'max:255'],
            'sales_tax_percent' => ['nullable', 'numeric', 'min:0'],
            'freight' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,issued,partially_received,fully_received,closed,cancelled'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item' => ['required', 'string'],
            'items.*.ordered_quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
