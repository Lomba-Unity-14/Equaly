<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(JobVacancyDataSeeder::class);
        $this->call(TrainingPartnerSeeder::class);

        User::create([
            'name' => 'Pengguna Demo',
            'email' => 'demo@equaly.id',
            'password' => Hash::make('password'),
        ]);
    }
}
