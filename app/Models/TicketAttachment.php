<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'file_name',
    ];

    /**
     * Relation: Ticket which belong to the attachment
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

}
