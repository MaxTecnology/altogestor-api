<?php

namespace App\Models;

use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoHistorico extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'solicitacao_documento_id',
        'documento_id',
        'user_id',
        'status',
        'observacao',
    ];

    public function solicitacao(): BelongsTo
    {
        return $this->belongsTo(SolicitacaoDocumento::class, 'solicitacao_documento_id');
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
