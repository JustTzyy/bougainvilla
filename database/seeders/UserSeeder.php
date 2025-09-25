<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the user
        $user = User::create([
            'firstName' => 'Justin',
            'middleName' => null,
            'lastName' => 'Digal',
            'name' => 'Justin Digal',
            'email' => 'justindigal@gmail.com',
            'password' => Hash::make('Justin123'),
            'contactNumber' => '09123456789',
            'birthday' => '1990-01-01',
            'age' => 34,
            'sex' => 'Male',
            'roleID' => 1, // Admin role
            'status' => 'Active',
        ]);

        // Create address for the user
        Address::create([
            'userID' => $user->id,
            'street' => '123 Main Street',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'zipcode' => '1000',
        ]);

        $this->command->info('User created successfully!');
        $this->command->info('Email: justindigal@gmail.com');
        $this->command->info('Password: Justin123');
        $this->command->info('Role: Admin (1)');
    }
}
