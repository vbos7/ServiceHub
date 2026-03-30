<?php

namespace Database\Seeders;

use App\Models\{Company, Project};
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            'Acme Corporation'  => ['Website Redesign', 'ERP Integration'],
            'Globex Systems'    => ['Mobile App v2', 'Infrastructure Migration'],
            'Initech Solutions' => ['Customer Portal'],
        ];

        foreach ($projects as $companyName => $names) {
            $company = Company::where('name', $companyName)->first();

            foreach ($names as $name) {
                Project::create([
                    'company_id' => $company->id,
                    'name'       => $name,
                ]);
            }
        }
    }
}
