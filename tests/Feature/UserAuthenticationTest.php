<?php

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::dropIfExists('users');
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('role')->default('user');
        $table->string('phone')->nullable();
        $table->string('address')->nullable();
        $table->string('image_path')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
});

it('stores account passwords as bcrypt hashes', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'user@example.test',
        'password' => 'correct-horse-battery',
    ]);

    $hash = $user->getRawOriginal('password');

    expect(Hash::info($hash)['algoName'])->toBe('bcrypt')
        ->and(Hash::check('correct-horse-battery', $hash))->toBeTrue();
});

it('authenticates a user against the bcrypt password in users', function () {
    User::create([
        'name' => 'Test User',
        'email' => 'user@example.test',
        'password' => 'correct-horse-battery',
        'role' => 'user',
    ]);

    $this->post(route('login'), [
        'email' => 'user@example.test',
        'password' => 'correct-horse-battery',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

it('redirects admins to the admin dashboard after login', function () {
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.test',
        'password' => 'correct-horse-battery',
        'role' => 'admin',
    ]);

    $this->post(route('login'), [
        'email' => 'admin@example.test',
        'password' => 'correct-horse-battery',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

it('logs users out and invalidates their authenticated session', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'user@example.test',
        'password' => 'correct-horse-battery',
        'role' => 'user',
    ]);

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect('/login');

    $this->assertGuest();
});

it('does not allow public registration to assign an admin role', function () {
    $this->post(route('register'), [
        'name' => 'New User',
        'email' => 'new-user@example.test',
        'password' => 'correct-horse-battery',
        'password_confirmation' => 'correct-horse-battery',
        'role' => 'admin',
    ])->assertRedirect(route('dashboard'));

    expect(User::where('email', 'new-user@example.test')->value('role'))->toBe('user');
});

it('creates administrator accounts and lists them separately from customers', function () {
    $admin = User::create([
        'name' => 'Current Admin',
        'email' => 'current-admin@example.test',
        'password' => 'correct-horse-battery',
        'role' => 'admin',
    ]);
    User::create([
        'name' => 'Existing Customer',
        'email' => 'customer@example.test',
        'password' => 'correct-horse-battery',
        'role' => 'user',
    ]);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'New Admin',
            'email' => 'new-admin@example.test',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
        ])
        ->assertRedirect(route('users.index'));

    expect(User::where('email', 'new-admin@example.test')->value('role'))->toBe('admin');

    $this->get(route('users.index'))
        ->assertOk()
        ->assertSee('new-admin@example.test')
        ->assertDontSee('customer@example.test');
});

it('creates customer records in users and keeps customers out of administrator CRUD', function () {
    $admin = User::create([
        'name' => 'Current Admin',
        'email' => 'current-admin@example.test',
        'password' => 'correct-horse-battery',
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->post(route('customers.store'), [
            'name' => 'New Customer',
            'email' => 'new-customer@example.test',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
            'phone' => '555-0100',
            'address' => '123 Main Street',
        ])
        ->assertRedirect(route('customers.index'));

    $customer = User::where('email', 'new-customer@example.test')->firstOrFail();

    expect($customer->getAttribute('role'))->toBe('user');

    $this->put(route('users.update', $customer), [
        'name' => 'Changed Name',
        'email' => $customer->email,
        'password' => '',
        'password_confirmation' => '',
    ])->assertNotFound();
});

it('keeps administrator accounts out of customer CRUD', function () {
    $admin = User::create([
        'name' => 'Current Admin',
        'email' => 'current-admin@example.test',
        'password' => 'correct-horse-battery',
        'role' => 'admin',
    ]);
    $targetAdmin = User::create([
        'name' => 'Target Admin',
        'email' => 'target-admin@example.test',
        'password' => 'correct-horse-battery',
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get(route('customers.edit', $targetAdmin))
        ->assertNotFound();
});
