<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobOrderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'person_id'               => $this->filled('person_id') ? $this->input('person_id') : null,
            'required_delivery_date'  => $this->sanitizeDateInput($this->input('required_delivery_date')),
            'issue_date'              => $this->sanitizeDateInput($this->input('issue_date')) ?: now()->toDateString(),
            'remarks'                 => $this->filled('remarks') ? $this->input('remarks') : null,
            'customer_po_reference'   => $this->filled('customer_po_reference') ? $this->input('customer_po_reference') : null,
            'subject'                 => $this->filled('subject') ? $this->input('subject') : null,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_order_number' => ['nullable', 'string', 'max:255'],
            'proforma_invoice_id' => ['required', 'exists:proforma_invoices,id'],
            'organization_id' => ['required', 'exists:organizations,id'],
            'person_id' => ['nullable', 'exists:persons,id'],
            'issue_date' => ['required', 'date'],
            'required_delivery_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'remarks' => ['nullable', 'string'],
            'customer_po_reference' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
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

    protected function sanitizeDateInput($value): ?string
    {
        $value = is_string($value) ? trim($value) : $value;

        if (empty($value) || $value === '0000-00-00') {
            return null;
        }

        if (! is_string($value)) {
            return null;
        }

        if (preg_match('/^-\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            $date = new \DateTime($value);

            if ((int) $date->format('Y') <= 1) {
                return null;
            }

            return $date->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
