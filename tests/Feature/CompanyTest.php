<?php

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
