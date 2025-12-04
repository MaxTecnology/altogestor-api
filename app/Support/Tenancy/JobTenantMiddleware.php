<?php

namespace App\Support\Tenancy;

class JobTenantMiddleware
{
    public function handle(object $job, callable $next): void
    {
        if (property_exists($job, 'tenantId') && $job->tenantId !== null) {
            $tenantId = (int) $job->tenantId;
            TenantManager::instance()->setTenantId($tenantId);
            config([
                'cache.prefix' => "tenant:{$tenantId}:cache",
                'database.redis.options.prefix' => "tenant:{$tenantId}:",
            ]);
        }

        $next($job);
    }
}
