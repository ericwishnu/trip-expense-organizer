<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Expense $expense): bool
    {
        $trip = $expense->tripDay?->trip;

        return $trip?->isCollaborator($user) || $trip?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Expense $expense): bool
    {
        return $expense->tripDay?->trip?->allowsEdit($user) ?? false;
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $expense->tripDay?->trip?->allowsEdit($user) ?? false;
    }
}
