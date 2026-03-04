<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoleType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@alphaacademy.com',
            'password' => Hash::make('password'),
            'role' => RoleType::SUPER_ADMIN->value,
        ]);

        // Create Departments
        $dme = Department::create(['name' => 'DME']);
        $ha = Department::create(['name' => 'HA']);

        // Create Department Heads
        User::create([
            'name' => 'DME Head',
            'email' => 'dmehead@alphaacademy.com',
            'password' => Hash::make('password'),
            'role' => RoleType::DME_HEAD->value,
            'department_id' => $dme->id,
        ]);

        User::create([
            'name' => 'HA Head',
            'email' => 'hahead@alphaacademy.com',
            'password' => Hash::make('password'),
            'role' => RoleType::HA_HEAD->value,
            'department_id' => $ha->id,
        ]);

        // Create Employees
        User::create([
            'name' => 'DME Employee',
            'email' => 'dmeemp@alphaacademy.com',
            'password' => Hash::make('password'),
            'role' => RoleType::EMPLOYEE->value,
            'department_id' => $dme->id,
        ]);

        User::create([
            'name' => 'HA Employee',
            'email' => 'haemp@alphaacademy.com',
            'password' => Hash::make('password'),
            'role' => RoleType::EMPLOYEE->value,
            'department_id' => $ha->id,
        ]);
    }
}
