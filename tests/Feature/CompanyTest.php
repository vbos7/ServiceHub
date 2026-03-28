<?php

use App\Models\{Company, User};

use function Pest\Laravel\{actingAs, assertDatabaseHas, assertDatabaseMissing};

it('should be able to create a company', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('companies.store'), [
            'name' => 'KPMG Brasil',
        ])
        ->assertRedirect();

    assertDatabaseHas('companies', [
        'name' => 'KPMG Brasil',
    ]);
});

it('should be able to list companies', function () {
    $user = User::factory()->create();

    Company::factory()->count(3)->create();

    actingAs($user)
        ->get(route('companies.index'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component('Companies/Index')
                ->has('companies', 3)
        );
});

it('should be able to update a company', function () {
    $user    = User::factory()->create();
    $company = Company::factory()->create(['name' => 'Nome Antigo']);

    actingAs($user)
        ->put(route('companies.update', $company), [
            'name' => 'Nome Atualizado',
        ])
        ->assertRedirect();

    assertDatabaseHas('companies', [
        'id'   => $company->id,
        'name' => 'Nome Atualizado',
    ]);
});

it('should be able to delete a company', function () {
    $user    = User::factory()->create();
    $company = Company::factory()->create();

    actingAs($user)
        ->delete(route('companies.destroy', $company))
        ->assertRedirect();

    assertDatabaseMissing('companies', [
        'id' => $company->id,
    ]);
});

it('should require a name to create a company', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('companies.store'), [
            'name' => '',
        ])
        ->assertSessionHasErrors(['name']);
});
