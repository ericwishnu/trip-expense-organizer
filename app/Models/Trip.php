<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'destination',
        'start_date',
        'end_date',
        'currency',
        'budget',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(TripDay::class);
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getRoleForUser(User $user): ?string
    {
        if ($this->user_id === $user->id) {
            return 'owner';
        }

        $collaborator = $this->collaborators
            ->firstWhere('id', $user->id);

        return $collaborator?->pivot?->role;
    }

    public function isCollaborator(User $user): bool
    {
        return $this->getRoleForUser($user) !== null;
    }

    public function allowsEdit(User $user): bool
    {
        $role = $this->getRoleForUser($user);

        return in_array($role, ['owner', 'editor'], true);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(TripInvite::class);
    }

    public function shareLinks(): HasMany
    {
        return $this->hasMany(TripShareLink::class);
    }

    public function exchangeRates(): HasMany
    {
        return $this->hasMany(TripExchangeRate::class);
    }

    public function getExchangeRateFor(string $currency): ?string
    {
        if (($this->currency ?? null) === $currency) {
            return '1';
        }

        $rate = $this->exchangeRates
            ->firstWhere('currency', $currency);

        return $rate?->rate !== null ? (string) $rate->rate : null;
    }
}
