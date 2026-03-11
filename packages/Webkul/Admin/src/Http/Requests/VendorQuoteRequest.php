<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
            'organization_id' => ['required', 'exists:organizations,id'],
            'person_id' => ['nullable', 'exists:persons,id'],
            'issue_date' => ['required', 'date'],
            'expected_response_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'requested', 'received', 'selected', 'rejected', 'cancelled'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.material_name' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
