<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitacaoDocumento extends Model
{
    use HasFactory, HasTenant, HasPublicId;

    protected $fillable = [
        'tenant_id',
        'public_id',
        'empresa_id',
        'modelo_documento_id',
        'competencia',
        'status',
        'gerada_automaticamente',
        'observacao',
    ];

    protected $casts = [
        'gerada_automaticamente' => 'bool',
    ];

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(ModeloDocumento::class, 'modelo_documento_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'solicitacao_documento_id');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(DocumentoHistorico::class, 'solicitacao_documento_id');
    }
}
