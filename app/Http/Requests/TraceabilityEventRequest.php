<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TraceabilityEventRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', 'min:2', 'max:80'],
            'occurred_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
