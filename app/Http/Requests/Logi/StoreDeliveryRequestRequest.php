<?php

namespace App\Http\Requests\Logi;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'farm_id' => ['required', 'integer'],
            'pickup_address' => ['required', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'cargo_description' => ['nullable', 'string', 'max:255'],
            'cargo_weight_kg' => ['nullable', 'numeric', 'min:0'],
            'requested_date' => ['nullable', 'date'],
        ];
    }
}
