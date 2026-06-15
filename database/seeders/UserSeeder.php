<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'user_number' => 012345,
                'name' => 'Md Abdur Rahman',
                'email' => 'abdur@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];


        // Insert or update users
        collect($users)->each(function ($user)
        {
            User::updateOrCreate(
                ['email' => $user['email']], // Unique identifier
                [
                    'user_number' => $user['user_number'],
                    'name' => $user['name'],
                    'password' => $user['password'],
                    'email_verified_at' => $user['email_verified_at'],
                ]
            );
        });
    }
}
