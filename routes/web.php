<?php

use App\Http\Controllers\{
    CompanyController,
    ProjectController,
    TicketController,
    TicketDetailController,
    UserProfileController,
};
use App\Models\{Company, Project, Ticket};
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('api-docs', fn () => response()->file(public_path('api-docs/index.html')))->name('api-docs');

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', fn () => inertia('Dashboard', [
        'stats' => [
            'companies' => Company::count(),
            'projects'  => Project::count(),
            'tickets'   => Ticket::count(),
        ],
    ]))->name('dashboard');

    // Companies
    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::put('companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    // Projects
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Tickets
    Route::get('tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::put('tickets/{ticket}/detail', [TicketDetailController::class, 'update'])->name('ticket-detail.update');

    // User Profile
    Route::get('user-profile', [UserProfileController::class, 'show'])->name('user-profile.show');
    Route::put('user-profile', [UserProfileController::class, 'update'])->name('user-profile.update');
});

require __DIR__ . '/settings.php';
