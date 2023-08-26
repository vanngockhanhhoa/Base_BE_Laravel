<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Uuids
{
    public function getHashKeyName(): ?string
    {
        return $this->hasField('hash_id') ? 'hash_id' : null;
    }

    /**
     * Boot functions from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function (Model $model) {
            if (empty($model->{$model->getHashKeyName()})) {
                $model->setAttribute($model->getHashKeyName(), Str::uuid()->toString());
            }
        });
    }
}
