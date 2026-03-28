<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTicketAttachment;
use App\Models\Ticket;
use Illuminate\Http\{RedirectResponse, Request};
use Inertia\{Inertia, Response};

class TicketController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tickets/Index', [
            'tickets' => Ticket::all(),
        ]);
    }

    public function show(Ticket $ticket): Response
    {
        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket->load('ticketDetail'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'project_id'  => 'required|exists:projects,id',
            'attachment'  => 'nullable|file|mimes:json,txt',
        ]);

        $data = [
            ...$request->only('title', 'description', 'project_id'),
            'user_id' => $request->user()->id,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        $ticket = Ticket::create($data);

        if ($ticket->attachment) {
            ProcessTicketAttachment::dispatch($ticket);
        }

        return redirect()->back();
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'project_id'  => 'required|exists:projects,id',
        ]);

        $ticket->update($request->only('title', 'description', 'project_id'));

        return redirect()->back();
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->back();
    }
}
