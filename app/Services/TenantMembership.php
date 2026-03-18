<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TenantMembership
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_FARMER = 'farmer';
    public const ROLE_BUYER = 'buyer';
    public const ROLE_TRUCKER = 'trucker';
    public const ROLE_AUDITOR = 'auditor';

    public static function allowedRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_FARMER,
            self::ROLE_BUYER,
            self::ROLE_TRUCKER,
            self::ROLE_AUDITOR,
        ];
    }

    public function listMembers(Tenant $tenant)
    {
        return TenantUser::query()
            ->where('tenant_id', $tenant->id)
            ->join('users', 'users.id', '=', 'tenant_users.user_id')
            ->select([
                'tenant_users.id as pivot_id',
                'tenant_users.role as role',
                'users.id as user_id',
                'users.name as name',
                'users.email as email',
                'tenant_users.created_at as added_at',
            ])
            ->orderBy('users.name')
            ->get();
    }

    public function addMemberByEmail(Tenant $tenant, string $email, string $role): TenantUser
    {
        $user = User::query()->where('email', $email)->firstOrFail();

        if (!in_array($role, self::allowedRoles(), true)) {
            throw new \InvalidArgumentException('Invalid role.');
        }

        return DB::transaction(function () use ($tenant, $user, $role) {
            return TenantUser::query()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $user->id],
                ['role' => $role]
            );
        });
    }

    public function updateRole(TenantUser $member, string $role): void
    {
        if (!in_array($role, self::allowedRoles(), true)) {
            throw new \InvalidArgumentException('Invalid role.');
        }

        $member->update(['role' => $role]);
    }

    public function removeMember(TenantUser $member): void
    {
        $member->delete();
    }
}
