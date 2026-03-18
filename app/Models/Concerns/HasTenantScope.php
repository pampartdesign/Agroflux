<?php

namespace App\Models\Concerns;

use App\Services\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

trait HasTenantScope
{
    protected static function bootHasTenantScope(): void
    {
        static::addGlobalScope(new class implements Scope {
            public function apply(Builder $builder, Model $model): void
            {
                $current = app(CurrentTenant::class);
                $tenantId = $current->id();

                if ($tenantId) {
                    $builder->where($model->getTable().'.tenant_id', $tenantId);
                }
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->tenant_id)) {
                $current = app(CurrentTenant::class);
                if ($current->id()) {
                    $model->tenant_id = $current->id();
                }
            }
        });
    }
}
