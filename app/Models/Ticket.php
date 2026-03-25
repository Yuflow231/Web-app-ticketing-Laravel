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
     * Relation: Project chich the ticket belongs to
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relation: Users working on the ticket
     */
    public function workers()
    {
        return $this->belongsToMany(User::class, 'ticket_workers')
                    ->withPivot('role', 'spent_time')
                    ->withTimestamps();
    }

    /**
     * Relation: Ticket creator
     */
    public function creator()
    {
        return $this->belongsToMany(User::class, 'ticket_workers')
                    ->wherePivot('role', 'Ticket Creator')
                    ->withTimestamps()
                    ->first();
    }

    /**
     * Relation: Attachments
     */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Scope tickets by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope ticket if active
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['New', 'In Progress']);
    }

    /**
     * Scope completed tickets
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Completed', 'Closed']);
    }

    /**
     * Scope billed tickets
     */
    public function scopeBilled($query)
    {
        return $query->where('type', 'Billed');
    }

}
