<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Notifications\TicketDetailEnriched;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessTicketAttachment implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Ticket $ticket)
    {
    }

    public function handle(): void
    {
        if (!$this->ticket->attachment || !Storage::disk('public')->exists($this->ticket->attachment)) {
            return;
        }

        $contents = Storage::disk('public')->get($this->ticket->attachment);

        $this->ticket->ticketDetail->update(['enriched_data' => $contents]);

        $this->ticket->user->notify(new TicketDetailEnriched($this->ticket));
    }
}
