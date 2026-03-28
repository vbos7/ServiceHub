<?php

use App\Models\{Company, Project, User};

use function Pest\Laravel\{actingAs, assertDatabaseHas, assertDatabaseMissing};

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

it('should be able to list projects', function () {
    $user    = User::factory()->create();
    $company = Company::factory()->create();

    Project::factory()->count(3)->for($company)->create();

    actingAs($user)
        ->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
            ->component('Projects/Index')
            ->has('projects', 3)
        );
});

it('should be able to update a project', function () {
    $user    = User::factory()->create();
    $project = Project::factory()->create(['name' => 'Nome Antigo']);

    actingAs($user)
        ->put(route('projects.update', $project), [
            'name'       => 'Nome Atualizado',
            'company_id' => $project->company_id,
        ])
        ->assertRedirect();

    assertDatabaseHas('projects', [
        'id'   => $project->id,
        'name' => 'Nome Atualizado',
    ]);
});

it('should be able to delete a project', function () {
    $user    = User::factory()->create();
    $project = Project::factory()->create();

    actingAs($user)
        ->delete(route('projects.destroy', $project))
        ->assertRedirect();

    assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

it('should require a name and company to create a project', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('projects.store'), [
            'name'       => '',
            'company_id' => '',
        ])
        ->assertSessionHasErrors(['name', 'company_id']);
});

it('should require a valid company to create a project', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('projects.store'), [
            'name'       => 'Projeto Alpha',
            'company_id' => 999,
        ])
        ->assertSessionHasErrors(['company_id']);
});
