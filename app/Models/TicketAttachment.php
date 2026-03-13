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

    /**
     * Get the whole file path
     */
    public function getFilePathAttribute(): string
    {
        return storage_path('app/public/attachments/' . $this->file_name);
    }

    /**
     * Get the public file path
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/attachments/' . $this->file_name);
    }
}
