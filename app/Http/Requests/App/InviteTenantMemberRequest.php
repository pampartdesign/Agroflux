<?php

namespace App\Http\Requests\App;

use App\Services\TenantMembership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteTenantMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'exists:users,email'],
            'role'  => ['required', 'string', Rule::in(TenantMembership::allowedRoles())],
        ];
    }
}
