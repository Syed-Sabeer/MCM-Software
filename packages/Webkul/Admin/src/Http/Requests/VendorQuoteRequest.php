<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
                'nullable',
                'exists:organizations,id',
                function ($attribute, $value, $fail) {
                    if (! $value) {
                        return;
                    }

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
            'status' => ['required', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.material_name' => ['nullable', 'string'],
            'items.*.item' => ['nullable', 'string'],
            'items.*.color' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'numeric', 'gt:0'],
            'items.*.ordered_quantity' => ['nullable', 'numeric', 'gt:0'],
            'items.*.unit' => ['nullable', 'string', 'max:100'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.vendor_id' => ['nullable', 'exists:organizations,id'],
            'items.*.requirement_id' => ['nullable', 'exists:job_order_requirements,id'],
            'items.*.id' => ['nullable', 'integer'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $organizationId = $this->input('organization_id');
            $lineVendorIds = collect($this->input('items', []))
                ->pluck('vendor_id')
                ->filter()
                ->values();

            if ($organizationId || $lineVendorIds->isNotEmpty()) {
                return;
            }

            $validator->errors()->add('organization_id', 'Please select a vendor or assign at least one line vendor.');
        });
    }
}
