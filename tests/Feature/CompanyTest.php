<?php

use App\Models\Company;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

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
        ->assertInertia(fn ($page) => $page
            ->component('Companies/Index')
            ->has('companies', 3)
        );
});
