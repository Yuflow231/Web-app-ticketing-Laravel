<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
        'progress_percent',
        'creation_date',
        'closing_date',
        'contract',
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
            'creation_date' => 'date',
            'closing_date' => 'date',
            'estimated_time' => 'decimal:2',
            'spent_time' => 'decimal:2',
            'progress_percent' => 'integer',
        ];
    }

    /**
     * Relation: Tickets of the project
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Relation: Member of the team's project
     */
    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Relation: Owner of the project
     */
    public function owner()
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->wherePivot('role', 'Owner')
                    ->withTimestamps();
    }

    /**
     * Relation: Maintainers of the project
     */
    public function maintainers()
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->wherePivot('role', 'Maintainer')
                    ->withTimestamps();
    }

    /**
     * Calculate the total time spent throughout all the tickets
     */
    public function calculateSpentTime()
    {
        $this->spent_time = $this->tickets()->sum('spent_time');
        $this->save();
    }

    /**
     * Scope for active projects
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['New', 'In Progress']);
    }

    /**
     * Scope for completed projects
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Completed', 'Closed']);
    }
}
