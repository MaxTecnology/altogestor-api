<?php

namespace App\Support\Tenancy;

use Illuminate\Support\Facades\App;

class TenantManager
{
    protected ?int $tenantId = null;

    protected ?string $tenantPublicId = null;

    public function setTenantId(int $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function setTenantPublicId(?string $publicId): void
    {
        $this->tenantPublicId = $publicId;
    }

    public function getTenantId(): ?int
    {
        return $this->tenantId;
    }

    public function getTenantPublicId(): ?string
    {
        return $this->tenantPublicId;
    }

    public function clear(): void
    {
        $this->tenantId = null;
        $this->tenantPublicId = null;
    }

    public static function instance(): self
    {
        return App::make(self::class);
    }
}
