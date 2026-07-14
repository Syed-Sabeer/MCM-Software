<?php

namespace Webkul\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProformaInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['billing', 'shipping'] as $type) {
            $key = $type . '_address';
            $address = $this->input($key);

            if (! is_array($address)) {
                $address = [];
            }

            $this->merge([
                $key => array_merge([
                    'key'      => null,
                    'label'    => null,
                    'type'     => $type,
                    'address'  => '',
                    'street'   => null,
                    'city'     => null,
                    'state'    => null,
                    'postcode' => null,
                    'country'  => null,
                ], $address, [
                    'type' => $address['type'] ?? $type,
                ]),
            ]);
        }
    }

    public function rules(): array
    {
        $quoteRule = $this->isMethod('post')
            ? ['required', 'exists:quotes,id']
            : ['nullable', 'exists:quotes,id'];

        return [
            'proforma_number'        => ['nullable', 'string', 'max:50'],
            'quote_id'               => $quoteRule,
            'organization_id'        => ['required', 'exists:organizations,id'],
            'person_id'              => ['nullable', 'exists:persons,id'],
            'sales_owner_id'         => ['nullable', 'exists:users,id'],
            'subject'                => ['nullable', 'string', 'max:255'],
            'issue_date'             => ['required', 'date'],
            'due_date'               => ['nullable', 'date'],
            'status'                 => ['nullable', 'string', 'max:50'],
            'customer_po_reference'  => ['nullable', 'string', 'max:255'],
            'notes'                  => ['nullable', 'string'],
            'terms'                  => ['nullable', 'string'],
            'payment_term'           => ['nullable', 'string', 'max:255'],
            'billing_address'        => ['nullable', 'array'],
            'billing_address.key'    => ['nullable', 'string', 'max:100'],
            'billing_address.label'  => ['nullable', 'string', 'max:255'],
            'billing_address.type'   => ['nullable', 'string', 'max:100'],
            'billing_address.address'=> ['nullable', 'string'],
            'shipping_address'       => ['nullable', 'array'],
            'shipping_address.key'   => ['nullable', 'string', 'max:100'],
            'shipping_address.label' => ['nullable', 'string', 'max:255'],
            'shipping_address.type'  => ['nullable', 'string', 'max:100'],
            'shipping_address.address'=> ['nullable', 'string'],
            'charges'                => ['nullable', 'array'],
            'charges.*.name'         => ['required_with:charges.*.type,charges.*.value', 'string', 'max:255'],
            'charges.*.type'         => ['required_with:charges.*.name,charges.*.value', 'in:percentage,value'],
            'charges.*.value'        => ['required_with:charges.*.name,charges.*.type', 'numeric', 'min:0'],
            'attachment'             => ['nullable', 'file', 'max:10240'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.id'             => ['nullable', 'integer'],
            'items.*.product_id'     => ['nullable', 'exists:products,id'],
            'items.*.color_variant_id' => ['nullable', 'integer'],
            'items.*.color_variant_name' => ['nullable', 'string', 'max:255'],
            'items.*.preview_image'  => ['nullable', 'string', 'max:2048'],
            'items.*.item_name'      => ['required', 'string', 'max:255'],
            'items.*.item_code'      => ['nullable', 'string', 'max:255'],
            'items.*.description'    => ['nullable', 'string'],
            'items.*.qty'            => ['required', 'numeric', 'gt:0'],
            'items.*.unit'           => ['nullable', 'string', 'max:100'],
            'items.*.unit_price'     => ['required', 'numeric', 'min:0'],
            'items.*.discount_amount'=> ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount'     => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $organizationType = DB::table('organizations')
                ->where('id', $this->input('organization_id'))
                ->value('type');

            if (! in_array($organizationType, ['customer', 'Customer'], true)) {
                $validator->errors()->add('organization_id', 'Organization must be customer type.');
            }

            if ($this->filled('person_id')) {
                $personOrgId = DB::table('persons')
                    ->where('id', $this->input('person_id'))
                    ->value('organization_id');

                if ((int) $personOrgId !== (int) $this->input('organization_id')) {
                    $validator->errors()->add('person_id', 'Selected person does not belong to selected organization.');
                }
            }
        });
    }
}
