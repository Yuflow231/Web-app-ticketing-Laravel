<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'project_id',
        'status',
        'priority',
        'type',
        'description',
        'estimated_time',
        'spent_time',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_time' => 'decimal:2',
            'spent_time' => 'decimal:2',
        ];
    }

    /**
     * Relation: Projet auquel appartient le ticket
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relation: Utilisateurs qui travaillent sur le ticket
     */
    public function workers()
    {
        return $this->belongsToMany(User::class, 'ticket_workers')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Relation: Créateur du ticket
     */
    public function creator()
    {
        return $this->belongsToMany(User::class, 'ticket_workers')
                    ->wherePivot('role', 'Ticket Creator')
                    ->withTimestamps()
                    ->first();
    }

    /**
     * Relation: Helpers du ticket
     */
    public function helpers()
    {
        return $this->belongsToMany(User::class, 'ticket_workers')
                    ->wherePivot('role', 'Helper')
                    ->withTimestamps();
    }

    /**
     * Relation: Pièces jointes
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Scope pour les tickets par priorité
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope pour les tickets actifs
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['New', 'In Progress']);
    }

    /**
     * Scope pour les tickets complétés
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Completed', 'Closed']);
    }

    /**
     * Scope pour les tickets facturés
     */
    public function scopeBilled($query)
    {
        return $query->where('type', 'Billed');
    }

    /**
     * Mettre à jour le temps passé du projet parent
     */
    protected static function booted()
    {
        static::saved(function ($ticket) {
            $ticket->project->calculateSpentTime();
        });

        static::deleted(function ($ticket) {
            $ticket->project->calculateSpentTime();
        });
    }
}
