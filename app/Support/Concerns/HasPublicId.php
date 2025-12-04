<?php

namespace App\Support\Concerns;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Uid\UuidV7;

trait HasPublicId
{
    public static function bootHasPublicId(): void
    {
        static::creating(function (Model $model): void {
            if (! $model->getAttribute('public_id')) {
                $model->setAttribute('public_id', (string) UuidV7::generate());
            }
        });
    }

    public function initializeHasPublicId(): void
    {
        if (property_exists($this, 'fillable')) {
            $this->fillable[] = 'public_id';
        }
    }
}
