<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@equaly.id'],
            [
                'name' => 'Demo Pengguna',
                'password' => Hash::make('password'),
                'date_of_birth' => '2001-05-15',
            ]
        );
    }
}
