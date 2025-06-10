<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function viewAny(User $user): bool
    {
        return $user->can('viewAny users') || $user->can('viewAny customers');
    }

    public function create(User $user): bool
    {
        return $user->can('create users') || $user->can('create customers');
    }

    public function update(User $user): bool
    {
        return $user->can('update users') || $user->can('update customers');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete users') || $user->can('delete customers');
    }
}
