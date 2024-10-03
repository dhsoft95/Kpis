<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'zendesk_id',
        'subject',
        'description',
        'status',
        'priority',
        'requester_id',
        'assignee_id',
        'ticket_created_at',
        'ticket_updated_at',
    ];

    protected $casts = [
        'ticket_created_at' => 'datetime',
        'ticket_updated_at' => 'datetime',
    ];
}
