<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Coordinator;
use Illuminate\Database\Seeder;

class CoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a user with the 'coordinator' status
        $user = User::create([
            'first_name' => 'Admin',
            'middle_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'), // Use a secure password
            'role' => 'coordinator',
        ]);

        // Create a coordinator entry linked to the user
        Coordinator::create([
            'user_id' => $user->id,
            'employee_id' => '12345', // You can change this as needed
            'contact_number' => '123-456-7890', // Adjust the contact number
            'course' => 'BSIT', // Adjust the course if needed
        ]);
    }
}
