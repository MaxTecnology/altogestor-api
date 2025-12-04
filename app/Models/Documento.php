<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Documento extends Model
{
    use HasFactory, HasTenant, HasPublicId;

    protected $fillable = [
        'tenant_id',
        'public_id',
        'solicitacao_documento_id',
        'user_id',
        'nome_original',
        'caminho',
        'mime_type',
        'tamanho_bytes',
        'origem',
        'status',
    ];

    public function solicitacao(): BelongsTo
    {
        return $this->belongsTo(SolicitacaoDocumento::class, 'solicitacao_documento_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(DocumentoHistorico::class, 'documento_id');
    }
}
