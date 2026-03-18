<?php

namespace App\Http\Requests\Logi;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'price' => ['required', 'numeric', 'min:0'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
