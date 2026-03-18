<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proforma_invoice_id' => ['required', 'exists:proforma_invoices,id'],
            'organization_id' => ['required', 'exists:organizations,id'],
            'person_id' => ['nullable', 'exists:persons,id'],
            'issue_date' => ['required', 'date'],
            'required_delivery_date' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,open,in_progress,ready_to_ship,completed,closed,cancelled'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.proforma_invoice_item_id' => ['nullable', 'integer'],
            'items.*.item_name' => ['required', 'string'],
            'items.*.item_code' => ['nullable', 'string'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.unit' => ['nullable', 'string'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.line_total' => ['nullable', 'numeric', 'min:0'],
            'items.*.rate' => ['nullable', 'numeric', 'min:0'],
            'items.*.amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
        ];
    }
}

