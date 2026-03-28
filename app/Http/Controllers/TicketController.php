<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\{RedirectResponse, Request};

class TicketController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'project_id'  => 'required|exists:projects,id',
        ]);

        Ticket::create([
            ...$request->only('title', 'description', 'project_id'),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back();
    }
}
