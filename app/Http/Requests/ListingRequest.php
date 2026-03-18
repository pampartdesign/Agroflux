<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListingRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'product_id'          => ['required', 'integer', 'exists:products,id'],
            'type'                => ['required', 'in:instock,preorder'],
            'price'               => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'available_qty'       => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'expected_harvest_at' => ['nullable', 'date'],
            'upfront_percent'     => ['required_if:type,preorder', 'numeric', 'min:1', 'max:99.99'],
            'is_active'           => ['nullable', 'boolean'],
        ];
    }
}
