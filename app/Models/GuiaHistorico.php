<?php

namespace App\Models;

use App\Support\Tenancy\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuiaHistorico extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'guia_id',
        'user_id',
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
