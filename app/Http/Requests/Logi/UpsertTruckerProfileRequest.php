<?php

namespace App\Http\Requests\Logi;

use Illuminate\Foundation\Http\FormRequest;

class UpsertTruckerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'vehicle_type' => ['required', 'in:van,pickup,refrigerated,truck'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
