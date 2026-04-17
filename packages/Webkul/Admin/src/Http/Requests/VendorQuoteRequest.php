<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
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
            'billing_address' => ['nullable', 'array'],
            'billing_address.key' => ['nullable', 'string', 'max:100'],
            'billing_address.label' => ['nullable', 'string', 'max:255'],
            'billing_address.type' => ['nullable', 'string', 'max:100'],
            'billing_address.address' => ['nullable', 'string'],
            'shipping_address' => ['nullable', 'array'],
            'shipping_address.key' => ['nullable', 'string', 'max:100'],
            'shipping_address.label' => ['nullable', 'string', 'max:255'],
            'shipping_address.type' => ['nullable', 'string', 'max:100'],
            'shipping_address.address' => ['nullable', 'string'],
            'issue_date' => ['required', 'date'],
            'expected_response_date' => ['nullable', 'date'],
            'payment_term' => ['nullable', 'string', 'max:255'],
            'shipping_method' => ['nullable', 'string', 'max:255'],
            'first_delivery_date' => ['nullable', 'date'],
            'last_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'charges' => ['nullable', 'array'],
            'charges.*.name' => ['required_with:charges.*.type,charges.*.value', 'string', 'max:255'],
            'charges.*.type' => ['required_with:charges.*.name,charges.*.value', 'in:percentage,value'],
            'charges.*.value' => ['required_with:charges.*.name,charges.*.type', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['draft', 'requested', 'received', 'selected', 'rejected', 'cancelled'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.material_name' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit' => ['nullable', 'string', 'max:100'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
