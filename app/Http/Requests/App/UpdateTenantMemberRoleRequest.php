<?php

namespace App\Http\Requests\App;

use App\Services\TenantMembership;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantMemberRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::in(TenantMembership::allowedRoles())],
        ];
    }
}
