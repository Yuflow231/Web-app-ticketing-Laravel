<?php

namespace App\Models;
use App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password_hashed',
        'role',
        'join_date',
        'language',
        'profile_pic',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hashed',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'join_date' => 'date',
        ];
    }

    /**
     * Override to use password_hashed instead of password
     */
    public function getAuthPassword()
    {
        return $this->password_hashed;
    }

    /**
     * Relation: Projects where the user is a member of
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_team')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Check whether the user has one of the given roles in a specific project.
     */
    public function hasProjectRole(int $projectId, string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->projects()
            ->where('projects.id', $projectId)
            ->wherePivotIn('role', $roles)
            ->exists();
    }

    /**
     * Check whether the user is part of a project team.
     */
    public function isInProjectTeam(int $projectId): bool
    {
        return $this->projects()
            ->where('projects.id', $projectId)
            ->exists();
    }



    /**
     * Relation: Tickets where the user os assigned to
     */
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_workers')
                    ->withPivot('role', 'spent_time')
                    ->withTimestamps();
    }

    /**
     * Check whether the user is part of a ticket team.
     */
    public function isInTicketTeam(int $ticketId): bool
    {
        return $this->tickets()
            ->where('tickets.id', $ticketId)
            ->exists();
    }

    /**
     * Check whether the user has one of the given roles in a specific ticket.
     */
    public function hasTicketRole(int $ticketId, string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->tickets()
            ->where('tickets.id', $ticketId)
            ->wherePivotIn('role', $roles)
            ->exists();
    }

    /**
     * Check if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Administrator';
    }

    /**
     * Get the full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
