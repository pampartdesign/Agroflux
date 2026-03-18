<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorReadingRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'sensor_id' => ['required', 'integer', 'exists:sensors,id'],
            'value' => ['nullable', 'numeric', 'min:-999999', 'max:999999'],
            'recorded_at' => ['required', 'date'],
        ];
    }
}
