<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Trainee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'Rainheart',
            'middle_name' => 'Llido',
            'last_name' => 'Galario',
            'email' => 'rainheart' . '@gmail.com',
            'email_verified_at' => now(),
            'role' => 'trainee',
            'password' => Hash::make('admin'),
        ]);

        Trainee::create([
            'user_id' => $user->id,
            'student_id' => '20203490',
            'contact_number' => '0912345678',
            'course' => 'BSIT',
        ]);
       
        $student_number = 20203491;  // Start with 20203491

        for ($i = 2; $i <= 10; $i++) {
            $user = User::create([
                'first_name' => 'PreUser',
                'middle_name' => 'Middle',
                'last_name' => 'Name' . $i,
                'email' => 'preuser' . $i . '@example.com',
                'email_verified_at' => now(),
                'role' => 'pre_user',
                'password' => Hash::make('password'),
            ]);

            Trainee::create([
                'user_id' => $user->id,
                'student_id' => $student_number++,
                'contact_number' => '0912345678' . $i,
                'course' => 'Course ' . $i,
            ]);
        }

        for ($i = 2; $i <= 10; $i++) {
            $user = User::create([
                'first_name' => 'Trainee',
                'middle_name' => 'Test',
                'last_name' => 'Name' . $i,
                'email' => 'trainee' . $i . '@example.com',
                'email_verified_at' => now(),
                'role' => 'trainee',
                'password' => Hash::make('password'),
            ]);

            Trainee::create([
                'user_id' => $user->id,
                'student_id' => $student_number++,
                'contact_number' => '0912345678' . $i,
                'course' => 'Course ' . $i,
            ]);
        }
    }
}
