<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeloDocumento extends Model
{
    use HasFactory, HasTenant, HasPublicId;

    protected $fillable = [
        'tenant_id',
        'public_id',
        'nome',
        'descricao',
        'departamento',
        'periodicidade',
        'obrigatorio',
        'exige_periodo',
    ];

    protected $casts = [
        'obrigatorio' => 'bool',
        'exige_periodo' => 'bool',
    ];

    public function solicitacoes(): HasMany
    {
        return $this->hasMany(SolicitacaoDocumento::class);
    }
}
