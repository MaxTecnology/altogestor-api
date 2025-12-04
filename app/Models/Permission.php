<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'name',
        'guard_name',
        'tenant_id',
        'public_id',
    ];
}
