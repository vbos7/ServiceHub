<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent: skip if already seeded
        if (User::where('email', 'demo@servicehub.test')->exists()) {
            return;
        }

        $user = User::create([
            'name'              => 'Demo User',
            'email'             => 'demo@servicehub.test',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ]);

        $user->userProfile->update([
            'phone'    => '+55 11 91234-5678',
            'position' => 'Systems Analyst',
        ]);

        $this->call([
            CompanySeeder::class,
            ProjectSeeder::class,
            TicketSeeder::class,
        ]);
    }
}
