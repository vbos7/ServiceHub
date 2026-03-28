<?php

namespace App\Models;

use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne};

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected $fillable = ['project_id', 'user_id', 'title', 'description', 'attachment'];

    protected static function booted(): void
    {
        static::created(function (Ticket $ticket): void {
            $ticket->ticketDetail()->create();
        });
    }

    /** @return HasOne<TicketDetail, $this> */
    public function ticketDetail(): HasOne
    {
        return $this->hasOne(TicketDetail::class);
    }

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
