<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TripShareLink extends Model
{
    protected $fillable = [
        'trip_id',
        'token',
        'expires_at',
        'created_by',
        'view_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $link): void {
            if (! $link->token) {
                $link->token = (string) Str::uuid();
            }
        });
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getShareUrlAttribute(): string
    {
        return route('trip-share.show', ['token' => $this->token]);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
