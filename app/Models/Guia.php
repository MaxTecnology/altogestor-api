<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guia extends Model
{
    use HasFactory, HasTenant, HasPublicId;

    protected $fillable = [
        'tenant_id',
        'public_id',
        'empresa_id',
        'tipo_obrigacao_id',
        'competencia',
        'vencimento',
        'valor',
        'status',
        'enviada_em',
        'paga_em',
        'observacao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'vencimento' => 'date',
        'enviada_em' => 'datetime',
        'paga_em' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tipoObrigacao(): BelongsTo
    {
        return $this->belongsTo(TipoObrigacao::class, 'tipo_obrigacao_id');
    }

    public function comprovantes(): HasMany
    {
        return $this->hasMany(ComprovantePagamento::class, 'guia_id');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(GuiaHistorico::class, 'guia_id');
    }
}
