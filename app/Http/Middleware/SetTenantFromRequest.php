<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Config;

class SetTenantFromRequest
{
    public function handle(Request $request, Closure $next)
    {
        $tenantManager = app(TenantManager::class);

        // 1) Usuário autenticado (painel/portal) leva prioridade
        $user = $request->user();
        if ($user && isset($user->tenant_id)) {
            $tenantManager->setTenantId((int) $user->tenant_id);
            $this->applyPrefixes((int) $user->tenant_id);

            return $next($request);
        }

        // 2) Token técnico com header X-Tenant-ID (public_id) — quando aplicável
        $tenantHeader = $request->header('X-Tenant-ID');
        if ($tenantHeader) {
            $tenant = Tenant::query()->where('public_id', $tenantHeader)->first();
            if ($tenant) {
                $tenantManager->setTenantPublicId($tenant->public_id);
                $tenantManager->setTenantId($tenant->id);
                $this->applyPrefixes((int) $tenant->id);
                return $next($request);
            }

            abort(400, 'tenant_header_invalid');
        }

        // Para rotas públicas (login/register/etc.) permitimos seguir sem tenant resolvido
        return $next($request);
    }

    protected function applyPrefixes(int $tenantId): void
    {
        // Prefixa cache e Redis para evitar colisão entre tenants
        Config::set('cache.prefix', "tenant:{$tenantId}:cache");
        Config::set('database.redis.options.prefix', "tenant:{$tenantId}:");
    }
}
