<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'category_id'         => ['nullable', 'integer', 'exists:catalog_categories,id'],
            'subcategory_id'      => ['nullable', 'integer', 'exists:catalog_categories,id'],
            'sku'                 => ['nullable', 'string', 'max:60'],
            'default_name'        => ['required', 'string', 'min:2', 'max:200'],
            'default_description' => ['nullable', 'string', 'max:5000'],
            'unit'                => ['nullable', 'string', 'max:30'],
            'stock_status'        => ['required', 'in:in_stock,pre_order'],
            'inventory'           => ['nullable', 'numeric', 'min:0'],
            'unit_price'          => ['nullable', 'numeric', 'min:0'],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
