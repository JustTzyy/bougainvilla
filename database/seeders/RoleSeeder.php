<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        Role::create([
            'role' => 'Admn',
            'description' => 'Administrator with full access',
        ]);

        Role::create([
            'role'=> 'Front Desk',
            'description' => 'Front Desk stuff with limited access',
        ]);
    }
}
