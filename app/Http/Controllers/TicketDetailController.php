<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\{RedirectResponse, Request};

class TicketDetailController extends Controller
{
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $ticket->ticketDetail->update($request->only('notes'));

        return redirect()->back()->with('success', 'Notes updated successfully.');
    }
}
