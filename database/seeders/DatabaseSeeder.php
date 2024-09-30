<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
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

        $role = Role::create([
            'name' => 'superadmin',
        ]);
        $role = Role::create([
            'name' => 'admin',
        ]);

        $role = Role::create([
            'name' => 'user',
        ]);

        $department = Department::create([
            'name' => 'SuperAdminConfig',
        ]);

        $department = Department::create([
            'name' => 'IT',
        ]);

        $department = Department::create([
            'name' => 'Sales',
        ]);

        User::create([
            'name' => 'TravelFreeTravels',
            'phone' => '9860000111',
            'email' => 'info@travelfreetravels.com',
            'password' => Hash::make('tft@12345'),
            'role_id' => 1,
            'department_id' => 1,
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'TravelFreeTravels-IT',
            'phone' => '9867261558',
            'email' => 'it@travelfreetravel.com',
            'password' => Hash::make('Testing@123'),
            'role_id' => 1,
            'department_id' => 1,
            'email_verified_at' => now(),
        ]);
    }
}
