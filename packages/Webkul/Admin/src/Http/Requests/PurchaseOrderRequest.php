<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseOrderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'person_id' => $this->filled('person_id') ? $this->input('person_id') : null,
            'job_order_id' => $this->filled('job_order_id') ? $this->input('job_order_id') : null,
            'vendor_quote_id' => $this->filled('vendor_quote_id') ? $this->input('vendor_quote_id') : null,
            'completion_date' => $this->sanitizeDateInput($this->input('completion_date')),
            'last_delivery_date' => $this->sanitizeDateInput($this->input('last_delivery_date')),
            'expected_receive_date' => $this->sanitizeDateInput($this->input('expected_receive_date')),
            'payment_term' => $this->filled('payment_term') ? $this->input('payment_term') : null,
            'shipping_method' => $this->filled('shipping_method') ? $this->input('shipping_method') : null,
            'notes' => $this->filled('notes') ? $this->input('notes') : null,
            'terms' => $this->filled('terms') ? $this->input('terms') : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'po_number' => ['nullable', 'string', 'max:255'],
            'organization_id' => [
                'nullable',
                'exists:organizations,id',
                function ($attribute, $value, $fail) {
                    if (! $value) {
                        return;
                    }

                    $type = DB::table('organizations')->where('id', $value)->value('type');
                    $normalized = mb_strtolower(trim((string) $type));

                    if (! in_array($normalized, ['vendor', 'vendors'], true)) {
                        $fail('Selected organization must be a vendor.');
                    }
                },
            ],
            'person_id' => ['nullable', 'exists:persons,id'],
            'job_order_id' => ['nullable', 'exists:job_orders,id'],
            'vendor_quote_id' => ['nullable', 'exists:vendor_quotes,id'],
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
            'completion_date' => ['nullable', 'date'],
            'last_delivery_date' => ['nullable', 'date'],
            'expected_receive_date' => ['nullable', 'date'],
            'payment_term' => ['nullable', 'string', 'max:255'],
            'shipping_method' => ['nullable', 'string', 'max:255'],
            'charges' => ['nullable', 'array'],
            'charges.*.name' => ['required_with:charges.*.type,charges.*.value', 'string', 'max:255'],
            'charges.*.type' => ['required_with:charges.*.name,charges.*.value', 'in:percentage,value'],
            'charges.*.value' => ['required_with:charges.*.name,charges.*.type', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'status' => [
                'required',
                'string',
                'max:50',
                Rule::exists('document_statuses', 'value')
                    ->where(fn ($query) => $query->where('type', 'purchase_order')),
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item' => ['required', 'string'],
            'items.*.ordered_quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.vendor_id' => [
                'nullable',
                'integer',
                Rule::exists('organizations', 'id')->where(function ($query) {
                    $query->whereRaw("LOWER(TRIM(type)) IN ('vendor', 'vendors')");
                }),
            ],
        ];
    }

    protected function sanitizeDateInput($value): ?string
    {
        $value = is_string($value) ? trim($value) : $value;

        if (empty($value)) {
            return null;
        }

        if (! is_string($value)) {
            return null;
        }

        if (preg_match('/^-\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        if (preg_match('/^0{4}-0{2}-0{2}$/', $value)) {
            return null;
        }

        try {
            $date = new \DateTime($value);

            if ((int) $date->format('Y') <= 1) {
                return null;
            }

            return $date->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
