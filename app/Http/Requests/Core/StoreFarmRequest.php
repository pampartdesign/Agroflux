<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'area_ha'  => ['nullable', 'numeric', 'min:0'],
            'notes'    => ['nullable', 'string', 'max:255'],
        ];
    }
}
