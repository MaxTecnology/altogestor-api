<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComprovantePagamento extends Model
{
    use HasFactory, HasTenant, HasPublicId;

    protected $table = 'comprovantes_pagamento';

    protected $fillable = [
        'tenant_id',
        'public_id',
        'guia_id',
        'user_id',
        'nome_original',
        'caminho',
        'mime_type',
        'tamanho_bytes',
        'status',
        'observacao',
    ];

    public function guia(): BelongsTo
    {
        return $this->belongsTo(Guia::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
