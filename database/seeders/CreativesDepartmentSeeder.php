<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoleType;

class CreativesDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Create Creatives Department
        $creatives = Department::firstOrCreate(['name' => 'Creatives']);

        // Create Creatives Head
        User::firstOrCreate(
            ['email' => 'creativeshead@alphaacademy.com'],
            [
                'name'          => 'Creatives Head',
                'password'      => Hash::make('password'),
                'role'          => RoleType::CREATIVES_HEAD->value,
                'department_id' => $creatives->id,
            ]
        );
    }
}
