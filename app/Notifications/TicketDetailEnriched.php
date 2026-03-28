<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketDetailEnriched extends Notification
{
    use Queueable;

    public function __construct(public readonly Ticket $ticket)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Ticket enriched: ' . $this->ticket->title)
            ->line('The ticket detail has been enriched with data from the attachment.')
            ->action('View Ticket', url('/tickets/' . $this->ticket->id));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
        ];
    }
}
