<?php

namespace App\Support\Tenancy;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

trait HasTenant
{
    public static function bootHasTenant(): void
    {
        static::creating(function (Model $model): void {
            $manager = TenantManager::instance();
            $tenantId = $manager->getTenantId();

            if ($tenantId === null) {
                throw new RuntimeException('Tenant ID is required to create this record.');
            }

            if (! $model->getAttribute('tenant_id')) {
                $model->setAttribute('tenant_id', $tenantId);
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder): void {
            $tenantId = TenantManager::instance()->getTenantId();

            if ($tenantId !== null) {
                $builder->where($builder->getModel()->getTable().'.tenant_id', $tenantId);
            }
        });
    }

    public function initializeHasTenant(): void
    {
        if (property_exists($this, 'fillable')) {
            $this->fillable[] = 'tenant_id';
        }
    }
}
