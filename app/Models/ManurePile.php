<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManurePile extends Model
{
    protected $fillable = ['name'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByRaw("CAST(REGEXP_REPLACE(name, '[^0-9]', '') AS UNSIGNED) ASC")
            ->orderBy('name');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(ManureTransfer::class, 'pile_id');
    }
}
