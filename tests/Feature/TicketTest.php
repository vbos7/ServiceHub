<?php

use App\Models\{Project, User};

use function Pest\Laravel\{actingAs, assertDatabaseHas};

it('should be able to create a ticket', function () {
    $user    = User::factory()->create();
    $project = Project::factory()->create();

    actingAs($user)
        ->post(route('tickets.store'), [
            'title'       => 'Servidor fora do ar',
            'description' => 'O servidor de produção caiu às 14h.',
            'project_id'  => $project->id,
        ])
        ->assertRedirect();

    assertDatabaseHas('tickets', [
        'title'      => 'Servidor fora do ar',
        'project_id' => $project->id,
        'user_id'    => $user->id,
    ]);
});
