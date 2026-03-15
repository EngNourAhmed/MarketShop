<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(PermissionSeeder::class);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'phone' => '01000000001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@trady.local'],
            [
                'name' => 'Admin',
                'phone' => '01000000000',
                'role' => 'admin',
                'password' => Hash::make('Admin@12345678'),
                'email_verified_at' => now(),
            ]
        );

        $allPermissionIds = Permission::query()->pluck('id')->all();
        $admin->permissionItems()->sync($allPermissionIds);
    }
}
