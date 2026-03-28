<?php

use App\Models\{Project, Ticket, User};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\{actingAs, assertDatabaseHas, assertDatabaseMissing, get, post};

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

it('should create a ticket detail when a ticket is created', function () {
    $user    = User::factory()->create();
    $project = Project::factory()->create();

    actingAs($user)
        ->post(route('tickets.store'), [
            'title'       => 'Bug no login',
            'description' => 'Usuário não consegue logar.',
            'project_id'  => $project->id,
        ]);

    $ticket = Ticket::first();

    expect($ticket->ticketDetail)->not->toBeNull();

    assertDatabaseHas('ticket_details', [
        'ticket_id' => $ticket->id,
    ]);
});

it('should be able to list tickets', function () {
    $user = User::factory()->create();

    Ticket::factory()->count(5)->create();

    actingAs($user)
        ->get(route('tickets.index'))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
            ->component('Tickets/Index')
            ->has('tickets', 5)
        );
});

it('should be able to view a single ticket with its detail', function () {
    $user   = User::factory()->create();
    $ticket = Ticket::factory()->create();

    actingAs($user)
        ->get(route('tickets.show', $ticket))
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
            ->component('Tickets/Show')
            ->has('ticket')
            ->has('ticket.ticket_detail')
        );
});

it('should be able to update a ticket', function () {
    $user   = User::factory()->create();
    $ticket = Ticket::factory()->create(['title' => 'Título Antigo']);

    actingAs($user)
        ->put(route('tickets.update', $ticket), [
            'title'       => 'Título Atualizado',
            'description' => $ticket->description,
            'project_id'  => $ticket->project_id,
        ])
        ->assertRedirect();

    assertDatabaseHas('tickets', [
        'id'    => $ticket->id,
        'title' => 'Título Atualizado',
    ]);
});

it('should be able to delete a ticket', function () {
    $user   = User::factory()->create();
    $ticket = Ticket::factory()->create();

    actingAs($user)
        ->delete(route('tickets.destroy', $ticket))
        ->assertRedirect();

    assertDatabaseMissing('tickets', [
        'id' => $ticket->id,
    ]);
});

it('should delete ticket detail when ticket is deleted', function () {
    $user   = User::factory()->create();
    $ticket = Ticket::factory()->create();

    $detailId = $ticket->ticketDetail->id;

    actingAs($user)
        ->delete(route('tickets.destroy', $ticket));

    assertDatabaseMissing('ticket_details', [
        'id' => $detailId,
    ]);
});

it('should require title, description and project to create a ticket', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('tickets.store'), [
            'title'       => '',
            'description' => '',
            'project_id'  => '',
        ])
        ->assertSessionHasErrors(['title', 'description', 'project_id']);
});

it('should require a valid project to create a ticket', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('tickets.store'), [
            'title'       => 'Bug',
            'description' => 'Descrição do bug.',
            'project_id'  => 999,
        ])
        ->assertSessionHasErrors(['project_id']);
});

it('should be able to create a ticket with an attachment', function () {
    $user    = User::factory()->create();
    $project = Project::factory()->create();

    $file = UploadedFile::fake()->create('dados.json', 100, 'application/json');

    actingAs($user)
        ->post(route('tickets.store'), [
            'title'       => 'Ticket com anexo',
            'description' => 'Descrição.',
            'project_id'  => $project->id,
            'attachment'  => $file,
        ])
        ->assertRedirect();

    $ticket = Ticket::first();

    expect($ticket->attachment)->not->toBeNull();
    Storage::disk('public')->assertExists($ticket->attachment);
});

it('should only accept json and text files as attachment', function () {
    $user    = User::factory()->create();
    $project = Project::factory()->create();

    $file = UploadedFile::fake()->create('foto.png', 100, 'image/png');

    actingAs($user)
        ->post(route('tickets.store'), [
            'title'       => 'Ticket com anexo inválido',
            'description' => 'Descrição.',
            'project_id'  => $project->id,
            'attachment'  => $file,
        ])
        ->assertSessionHasErrors(['attachment']);
});

it('should not allow unauthenticated user to create a ticket', function () {
    $project = Project::factory()->create();

    post(route('tickets.store'), [
        'title'       => 'Ticket sem login',
        'description' => 'Descrição.',
        'project_id'  => $project->id,
    ])->assertRedirect(route('login'));
});

it('should not allow unauthenticated user to list tickets', function () {
    get(route('tickets.index'))
        ->assertRedirect(route('login'));
});
