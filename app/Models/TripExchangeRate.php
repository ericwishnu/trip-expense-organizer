<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripExchangeRate extends Model
{
    protected $fillable = [
        'trip_id',
        'currency',
        'rate',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:6',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
