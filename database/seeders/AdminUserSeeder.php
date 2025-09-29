<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // change in prod
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => 'admin',
            ]
        );

        // Employee
        User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee',
                'password' => Hash::make('password'), // change in prod
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => 'employee',
            ]
        );
    }
}
