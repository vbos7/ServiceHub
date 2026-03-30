<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTicketAttachment;
use App\Models\{Project, Ticket};
use Illuminate\Http\{RedirectResponse, Request};
use Inertia\{Inertia, Response};

class TicketController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Tickets/Index', [
            'tickets'  => Ticket::with(['project', 'user'])->latest()->get(),
            'projects' => Project::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Ticket $ticket): Response
    {
        return Inertia::render('Tickets/Show', [
            'ticket'   => $ticket->load(['ticketDetail', 'project', 'user']),
            'projects' => Project::orderBy('name')->get(['id', 'name']),
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

        return redirect()->back()->with('success', 'Ticket created successfully.');
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'project_id'  => 'required|exists:projects,id',
        ]);

        $ticket->update($request->only('title', 'description', 'project_id'));

        return redirect()->back()->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->back()->with('success', 'Ticket deleted successfully.');
    }
}
