<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Admin role (which is now the highest level)
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found. Please run RoleSeeder first!');
            return;
        }

        // Create 2 Admin users (level 1 - full access)
        $admins = [
            [
                'name' => 'Admin 1',
                'email' => 'admin1@sipaduhok.com',
                'username' => 'admin1',
                'password' => Hash::make('password'), // Change in production!
                'phone' => '081234567890',
                'role' => 'admin', // Keep enum for backward compatibility
                'role_id' => $adminRole->id, // New role system
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin 2',
                'email' => 'admin2@sipaduhok.com',
                'username' => 'admin2',
                'password' => Hash::make('password'), // Change in production!
                'phone' => '081234567891',
                'role' => 'admin', // Keep enum for backward compatibility
                'role_id' => $adminRole->id, // New role system
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $adminData) {
            User::updateOrCreate(
                ['email' => $adminData['email']],
                $adminData
            );
        }

        $this->command->info('Admin users created successfully!');
        $this->command->warn('Default credentials:');
        $this->command->warn('  Email: admin1@sipaduhok.com / admin2@sipaduhok.com');
        $this->command->warn('  Password: password');
        $this->command->warn('Please change these passwords in production!');
    }
}
