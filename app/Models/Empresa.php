<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Empresa extends Model
{
    use HasFactory, HasTenant, HasPublicId;

    protected $fillable = [
        'tenant_id',
        'public_id',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'inscricao_municipal',
        'timezone',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('perfil')
            ->withTimestamps();
    }
}
