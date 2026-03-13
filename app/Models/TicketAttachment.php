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
     * Relation: Ticket auquel appartient la pièce jointe
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Obtenir le chemin complet du fichier
     */
    public function getFilePathAttribute(): string
    {
        return storage_path('app/public/attachments/' . $this->file_name);
    }

    /**
     * Obtenir l'URL publique du fichier
     */
    public function getFileUrlAttribute(): string
    {
        return asset('storage/attachments/' . $this->file_name);
    }
}
