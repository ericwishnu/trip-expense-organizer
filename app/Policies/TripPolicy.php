<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Trip $trip): bool
    {
        return $trip->isCollaborator($user) || $trip->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Trip $trip): bool
    {
        return $trip->allowsEdit($user);
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $trip->user_id === $user->id;
    }
}
