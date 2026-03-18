<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'min:2', 'max:150'],
            'city'          => ['nullable', 'string', 'max:120'],
            'area_ha'       => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'notes'         => ['nullable', 'string', 'max:2000'],
            'region_id'     => ['nullable', 'integer', 'exists:regions,id'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
