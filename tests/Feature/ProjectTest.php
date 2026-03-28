<?php

use App\Models\{Company, User};

use function Pest\Laravel\{actingAs, assertDatabaseHas};

it('should be able to create a project for a company', function () {
    $user    = User::factory()->create();
    $company = Company::factory()->create();

    actingAs($user)
        ->post(route('projects.store'), [
            'name'       => 'Projeto Alpha',
            'company_id' => $company->id,
        ])
        ->assertRedirect();

    assertDatabaseHas('projects', [
        'name'       => 'Projeto Alpha',
        'company_id' => $company->id,
    ]);
});
