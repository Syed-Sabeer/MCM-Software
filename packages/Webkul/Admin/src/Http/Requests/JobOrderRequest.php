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
            'items.*.item_name' => ['required', 'string'],
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
        ];
    }
}
