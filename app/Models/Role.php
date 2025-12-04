<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'name',
        'guard_name',
        'tenant_id',
        'public_id',
    ];
}
