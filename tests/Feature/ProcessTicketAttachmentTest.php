<?php

use App\Jobs\ProcessTicketAttachment;
use App\Models\{Project, Ticket, User};
use App\Notifications\TicketDetailEnriched;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Notification, Queue, Storage};

use function Pest\Laravel\actingAs;

it('should dispatch job when ticket is created with attachment', function () {
    Queue::fake();

    $user    = User::factory()->create();
    $project = Project::factory()->create();
    $file    = UploadedFile::fake()->create('dados.json', 100, 'application/json');

    actingAs($user)
        ->post(route('tickets.store'), [
            'title'       => 'Ticket com anexo',
            'description' => 'Descrição.',
            'project_id'  => $project->id,
            'attachment'  => $file,
        ]);

    Queue::assertPushed(ProcessTicketAttachment::class);
});

it('should not dispatch job when ticket is created without attachment', function () {
    Queue::fake();

    $user    = User::factory()->create();
    $project = Project::factory()->create();

    actingAs($user)
        ->post(route('tickets.store'), [
            'title'       => 'Ticket sem anexo',
            'description' => 'Descrição.',
            'project_id'  => $project->id,
        ]);

    Queue::assertNotPushed(ProcessTicketAttachment::class);
});

it('should enrich ticket detail when job is processed', function () {
    Storage::fake('public');

    $ticket = Ticket::factory()->create();
    $path   = 'attachments/dados.json';

    Storage::disk('public')->put($path, json_encode([
        'priority' => 'high',
        'category' => 'infrastructure',
    ]));

    $ticket->update(['attachment' => $path]);

    (new ProcessTicketAttachment($ticket))->handle();

    $ticket->ticketDetail->refresh();

    expect($ticket->ticketDetail->enriched_data)->not->toBeNull();
    expect($ticket->ticketDetail->enriched_data)->toContain('high');
});

it('should notify user when job is processed', function () {
    Notification::fake();
    Storage::fake('public');

    $ticket = Ticket::factory()->create();
    $path   = 'attachments/dados.json';

    Storage::disk('public')->put($path, json_encode(['priority' => 'low']));
    $ticket->update(['attachment' => $path]);

    (new ProcessTicketAttachment($ticket))->handle();

    Notification::assertSentTo($ticket->user, TicketDetailEnriched::class);
});

it('should handle invalid attachment gracefully', function () {
    Storage::fake('public');

    $ticket = Ticket::factory()->create();
    $ticket->update(['attachment' => 'attachments/nao-existe.json']);

    (new ProcessTicketAttachment($ticket))->handle();

    $ticket->ticketDetail->refresh();

    expect($ticket->ticketDetail->enriched_data)->toBeNull();
});
