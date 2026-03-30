<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            'Acme Corporation',
            'Globex Systems',
            'Initech Solutions',
        ];

        foreach ($companies as $name) {
            Company::create(['name' => $name]);
        }
    }
}
