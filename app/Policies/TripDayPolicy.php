<?php

namespace App\Policies;

use App\Models\TripDay;
use App\Models\User;

class TripDayPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TripDay $tripDay): bool
    {
        return $tripDay->trip?->isCollaborator($user) || $tripDay->trip?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TripDay $tripDay): bool
    {
        return $tripDay->trip?->allowsEdit($user) ?? false;
    }

    public function delete(User $user, TripDay $tripDay): bool
    {
        return $tripDay->trip?->allowsEdit($user) ?? false;
    }
}
