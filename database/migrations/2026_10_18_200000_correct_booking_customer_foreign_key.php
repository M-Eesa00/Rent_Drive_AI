<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $foreignKey = $this->customerForeignKey();

        if ($foreignKey['foreign_table'] === 'users') {
            return;
        }

        if ($foreignKey['foreign_table'] !== 'customers') {
            throw new RuntimeException('The bookings.customer_id foreign key references an unexpected table.');
        }

        $idMap = [];

        DB::table('customers')->orderBy('id')->chunkById(100, function ($legacyCustomers) use (&$idMap) {
            foreach ($legacyCustomers as $legacyCustomer) {
                $user = DB::table('users')->where('email', $legacyCustomer->email)->first();
                if ($user && $user->role !== 'user') {
                    throw new RuntimeException("Legacy customer email {$legacyCustomer->email} belongs to a non-customer user.");
                }

                $idMap[$legacyCustomer->id] = $user
                    ? $user->id
                    : DB::table('users')->insertGetId($this->userAttributes($legacyCustomer));
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        $this->updateBookingCustomerIds($idMap);

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('customer_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        $foreignKey = $this->customerForeignKey();

        if ($foreignKey['foreign_table'] === 'customers') {
            return;
        }

        if ($foreignKey['foreign_table'] !== 'users') {
            throw new RuntimeException('The bookings.customer_id foreign key references an unexpected table.');
        }

        $userIds = DB::table('bookings')
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');
        $idMap = [];

        foreach ($userIds as $userId) {
            $user = DB::table('users')->find($userId);
            if (! $user) {
                throw new RuntimeException("Booking customer user {$userId} was not found.");
            }

            $customer = DB::table('customers')->where('email', $user->email)->first();
            $idMap[$userId] = $customer
                ? $customer->id
                : DB::table('customers')->insertGetId($this->legacyCustomerAttributes($user));
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        $this->updateBookingCustomerIds($idMap);

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->nullOnDelete();
        });
    }

    private function customerForeignKey(): array
    {
        foreach (Schema::getForeignKeys('bookings') as $foreignKey) {
            if ($foreignKey['columns'] === ['customer_id']) {
                return $foreignKey;
            }
        }

        throw new RuntimeException('The bookings.customer_id foreign key was not found.');
    }

    private function updateBookingCustomerIds(array $idMap): void
    {
        foreach ($idMap as $oldId => $newId) {
            DB::table('bookings')
                ->where('customer_id', $oldId)
                ->update(['customer_id' => $newId]);
        }
    }

    private function userAttributes(object $customer): array
    {
        $attributes = [
            'name' => $customer->name,
            'email' => $customer->email,
            'password' => $customer->password ?: Hash::make(Str::random(64)),
            'role' => 'user',
            'phone' => $customer->phone,
            'address' => $customer->address,
            'image_path' => $customer->image_path,
            'created_at' => $customer->created_at ?: now(),
            'updated_at' => $customer->updated_at ?: now(),
        ];

        return array_filter($attributes, fn ($value) => $value !== null);
    }

    private function legacyCustomerAttributes(object $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password ?: Hash::make(Str::random(64)),
            'phone' => $user->phone ?? '',
            'address' => $user->address,
            'image_path' => $user->image_path,
            'created_at' => $user->created_at ?: now(),
            'updated_at' => $user->updated_at ?: now(),
        ];
    }
};
