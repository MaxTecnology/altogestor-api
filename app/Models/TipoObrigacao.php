<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoObrigacao extends Model
{
    use HasFactory, HasTenant, HasPublicId;

    protected $fillable = [
        'tenant_id',
        'public_id',
        'nome',
        'descricao',
        'departamento',
        'periodicidade',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'bool',
    ];

    public function configuracoes(): HasMany
    {
        return $this->hasMany(ConfigObrigacaoEmpresa::class, 'tipo_obrigacao_id');
    }

    public function guias(): HasMany
    {
        return $this->hasMany(Guia::class, 'tipo_obrigacao_id');
    }
}
