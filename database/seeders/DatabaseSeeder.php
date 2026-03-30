<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent: skip if already seeded
        if (User::where('email', 'demo@servicehub.test')->exists()) {
            return;
        }

        $user = User::factory()->create([
            'name'  => 'Demo User',
            'email' => 'demo@servicehub.test',
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
