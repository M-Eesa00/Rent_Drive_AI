<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/_admin-only-test', fn () => response()->noContent())
        ->middleware(['web', 'auth', 'admin-only']);
});

it('requires authentication before reaching an admin-only route', function () {
    $this->get('/_admin-only-test')->assertRedirect(route('login'));
});

it('forbids authenticated users without the admin role', function () {
    $user = new User(['role' => 'user']);

    $this->actingAs($user)
        ->get('/_admin-only-test')
        ->assertForbidden();
});

it('allows users with the admin role', function () {
    $admin = new User(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/_admin-only-test')
        ->assertNoContent();
});
