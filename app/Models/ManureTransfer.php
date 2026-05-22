<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManureTransfer extends Model
{
    // Status constants for cleaner code
    const STATUS_PENDING = 'pending';
    const STATUS_RECEIVED = 'received';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'transfer_no',
        'farm_id',
        'license_plate',
        'weight',
        'out_datetime',
        'out_photo',
        'out_user_id',
        'received_datetime',
        'pile_id',
        'receive_photo',
        'receive_user_id',
        'status',
        'remark'
    ];

    protected $casts = [
        'out_datetime' => 'datetime',
        'received_datetime' => 'datetime',
        'weight' => 'decimal:2'
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function pile(): BelongsTo
    {
        return $this->belongsTo(ManurePile::class, 'pile_id');
    }

    public function outUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'out_user_id');
    }

    public function receiveUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receive_user_id');
    }
}
