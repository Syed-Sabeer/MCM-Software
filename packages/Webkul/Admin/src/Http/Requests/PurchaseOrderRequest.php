<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'organization_id' => ['required', 'exists:organizations,id'],
            'person_id' => ['nullable', 'exists:persons,id'],
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
            'vendor_quote_id' => ['nullable', 'exists:vendor_quotes,id'],
            'expected_receive_date' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,issued,partially_received,fully_received,closed,cancelled'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item' => ['required', 'string'],
            'items.*.ordered_quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
