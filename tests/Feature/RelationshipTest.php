<?php

use App\Models\{Company, Project, Ticket, TicketDetail, User, UserProfile};

use function Pest\Laravel\assertDatabaseMissing;

it('company has many projects', function () {
    $company = Company::factory()->create();

    Project::factory()->count(3)->for($company)->create();

    expect($company->projects)->toHaveCount(3);
    expect($company->projects->first())->toBeInstanceOf(Project::class);
});

it('project belongs to a company', function () {
    $project = Project::factory()->create();

    expect($project->company)->toBeInstanceOf(Company::class);
});

it('project has many tickets', function () {
    $project = Project::factory()->create();

    Ticket::factory()->count(4)->for($project)->create();

    expect($project->tickets)->toHaveCount(4);
    expect($project->tickets->first())->toBeInstanceOf(Ticket::class);
});

it('ticket belongs to a project', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->project)->toBeInstanceOf(Project::class);
});

it('ticket belongs to a user', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->user)->toBeInstanceOf(User::class);
});

it('ticket has one ticket detail', function () {
    $ticket = Ticket::factory()->create();

    expect($ticket->ticketDetail)->not->toBeNull();
    expect($ticket->ticketDetail)->toBeInstanceOf(TicketDetail::class);
});

it('user has one user profile', function () {
    $user = User::factory()->create();

    expect($user->userProfile)->not->toBeNull();
    expect($user->userProfile)->toBeInstanceOf(UserProfile::class);
});

it('deleting a company deletes its projects', function () {
    $company = Company::factory()->create();

    Project::factory()->count(2)->for($company)->create();

    $company->delete();

    assertDatabaseMissing('projects', ['company_id' => $company->id]);
});

it('deleting a project deletes its tickets', function () {
    $project = Project::factory()->create();

    Ticket::factory()->count(2)->for($project)->create();

    $project->delete();

    assertDatabaseMissing('tickets', ['project_id' => $project->id]);
});
