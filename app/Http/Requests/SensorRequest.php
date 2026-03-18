<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'gateway_id' => ['nullable', 'integer', 'exists:iot_gateways,id'],
            'group_key' => ['required', 'string', 'min:2', 'max:50'],
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'identifier' => ['nullable', 'string', 'max:120'],
            'unit' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:online,offline'],
        ];
    }
}
