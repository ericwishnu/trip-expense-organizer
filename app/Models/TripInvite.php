<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TripInvite extends Model
{
    protected $fillable = [
        'trip_id',
        'token',
        'role',
        'expires_at',
        'created_by',
        'accepted_by',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $invite): void {
            if (! $invite->token) {
                $invite->token = (string) Str::uuid();
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

    public function accepter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function getInviteUrlAttribute(): string
    {
        return route('trip-invites.accept', ['token' => $this->token]);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
