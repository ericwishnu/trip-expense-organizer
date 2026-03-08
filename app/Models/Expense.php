<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'trip_day_id',
        'title',
        'category',
        'amount',
        'currency',
        'spent_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'spent_at' => 'datetime',
        ];
    }

    public function tripDay(): BelongsTo
    {
        return $this->belongsTo(TripDay::class);
    }
}
