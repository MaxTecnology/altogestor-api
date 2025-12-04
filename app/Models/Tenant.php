<?php

namespace App\Models;

use App\Support\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'nome',
        'slug',
        'public_id',
    ];
}
