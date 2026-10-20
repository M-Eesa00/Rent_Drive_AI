<?php

namespace App\Policies;

use App\Models\Bookings;
use App\Models\User;

class BookingsPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'user'], true);
    }

    public function view(User $user, Bookings $booking): bool
    {
        return $user->role === 'admin' || $user->id === $booking->customer_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['user', 'admin'], true);
    }

    public function update(User $user, Bookings $booking): bool
    {
        return $user->role === 'admin' || $user->id === $booking->customer_id;
    }

    public function delete(User $user, Bookings $booking): bool
    {
        return $user->role === 'admin' || $user->id === $booking->customer_id;
    }
}
