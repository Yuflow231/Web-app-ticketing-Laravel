<?php

namespace App\Models;

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
     * Override pour utiliser password_hashed au lieu de password
     */
    public function getAuthPassword()
    {
        return $this->password_hashed;
    }

    /**
     * Relation: Projects où l'utilisateur est membre
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_team')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Relation: Tickets où l'utilisateur travaille
     */
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_workers')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === 'Administrator';
    }

    /**
     * Obtenir le nom complet
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
