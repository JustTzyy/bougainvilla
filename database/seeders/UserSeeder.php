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

        // Create FrontDesk user
        $frontDeskUser = User::create([
            'firstName' => 'Jackie Claire',
            'middleName' => 'Arca',
            'lastName' => 'Poledo',
            'name' => 'Jackie Claire Poledo',
            'email' => 'jackieClairePoledo@gmail.com',
            'password' => Hash::make('Jackie123'),
            'contactNumber' => '09123456790',
            'birthday' => '1995-05-15',
            'age' => 29,
            'sex' => 'Female',
            'roleID' => 2, // FrontDesk role
            'status' => 'Active',
        ]);

        // Create address for the FrontDesk user
        Address::create([
            'userID' => $frontDeskUser->id,
            'street' => '456 FrontDesk Street',
            'city' => 'Manila',
            'province' => 'Metro Manila',
            'zipcode' => '1001',
        ]);

        $this->command->info('Users created successfully!');
        $this->command->info('Admin - Email: justindigal@gmail.com, Password: Justin123, Role: Admin (1)');
        $this->command->info('FrontDesk - Email: jackieClairePoledo@gmail.com, Password: Jackie123, Role: FrontDesk (2)');
    }
}
