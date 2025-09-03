<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Check if roles already exist
        if (!Role::where('name', 'super-admin')->exists()) {
            $superAdminRole = Role::create(['name' => 'super-admin']);
        }

        if (!Role::where('name', 'admin')->exists()) {
            $adminRole = Role::create(['name' => 'admin']);
        }

        // Check if super admin user already exists
        if (!User::where('email', 'support@mail.com')->exists()) {
            $superAdmin = User::create([
                'name' => 'Support',
                'email' => 'support@mail.com',
                'password' => bcrypt('Mitra123'),
                'email_verified_at' => now(),
            ]);
            $superAdmin->assignRole('super-admin');
        }
    }
}
