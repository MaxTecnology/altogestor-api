<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigObrigacaoEmpresa extends Model
{
    use HasFactory, HasTenant, HasPublicId;

    protected $fillable = [
        'tenant_id',
        'public_id',
        'empresa_id',
        'tipo_obrigacao_id',
        'vencimento_dia',
        'gera_guia_automatica',
        'responsavel_user_id',
    ];

    protected $casts = [
        'gera_guia_automatica' => 'bool',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tipoObrigacao(): BelongsTo
    {
        return $this->belongsTo(TipoObrigacao::class, 'tipo_obrigacao_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_user_id');
    }
}
