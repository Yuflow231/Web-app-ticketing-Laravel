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
     * Relation: Tickets du projet
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Relation: Membres de l'équipe du projet
     */
    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Relation: Propriétaires du projet
     */
    public function owners()
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->wherePivot('role', 'Owner')
                    ->withTimestamps();
    }

    /**
     * Relation: Mainteneurs du projet
     */
    public function maintainers()
    {
        return $this->belongsToMany(User::class, 'project_team')
                    ->wherePivot('role', 'Maintainer')
                    ->withTimestamps();
    }

    /**
     * Calculer le temps total passé sur tous les tickets
     */
    public function calculateSpentTime()
    {
        $this->spent_time = $this->tickets()->sum('spent_time');
        $this->save();
    }

    /**
     * Scope pour les projets actifs
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['New', 'In Progress']);
    }

    /**
     * Scope pour les projets complétés
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Completed', 'Closed']);
    }
}
