<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchRequest extends FormRequest
{
    public function authorize(): bool { return (bool) $this->user(); }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'code' => ['required', 'string', 'min:2', 'max:80'],
            'status' => ['required', 'in:draft,published,archived'],
        ];
    }
}
