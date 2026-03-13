{{-- resources/views/dashBoard.blade.php --}}
@extends('layout.main')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Tableau de bord</h1>
        <p>Bienvenue, {{ Auth::user()->full_name }}</p>
    </div>

    {{-- Statistiques --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <h3>{{ $stats['total_projects'] }}</h3>
                <p>Projets totaux</p>
            </div>
        </div>
        
        <div class="stat-card active">
            <div class="stat-icon">🚀</div>
            <div class="stat-content">
                <h3>{{ $stats['active_projects'] }}</h3>
                <p>Projets actifs</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🎫</div>
            <div class="stat-content">
                <h3>{{ $stats['total_tickets'] }}</h3>
                <p>Tickets totaux</p>
            </div>
        </div>
        
        <div class="stat-card active">
            <div class="stat-icon">⚡</div>
            <div class="stat-content">
                <h3>{{ $stats['active_tickets'] }}</h3>
                <p>Tickets actifs</p>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        {{-- Mes tickets actifs --}}
        <div class="section">
            <div class="section-header">
                <h2>Mes tickets actifs</h2>
                <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                    <span>+</span> Nouveau ticket
                </a>
            </div>
            
            @if($myTickets->count() > 0)
                <div class="tickets-list">
                    @foreach($myTickets as $ticket)
                        <div class="ticket-card">
                            <div class="ticket-header">
                                <h3>
                                    <a href="{{ route('tickets.show', $ticket) }}">
                                        {{ $ticket->name }}
                                    </a>
                                </h3>
                                <span class="badge badge-{{ strtolower($ticket->priority) }}">
                                    {{ $ticket->priority }}
                                </span>
                            </div>
                            
                            <p class="ticket-project">
                                <strong>Projet:</strong> 
                                <a href="{{ route('projects.show', $ticket->project) }}">
                                    {{ $ticket->project->name }}
                                </a>
                            </p>
                            
                            <div class="ticket-meta">
                                <span class="status status-{{ strtolower(str_replace(' ', '-', $ticket->status)) }}">
                                    {{ $ticket->status }}
                                </span>
                                <span class="type">{{ $ticket->type }}</span>
                                @if($ticket->estimated_time)
                                    <span class="time">
                                        {{ $ticket->spent_time }}h / {{ $ticket->estimated_time }}h
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="empty-state">Aucun ticket actif pour le moment.</p>
            @endif
        </div>

        {{-- Mes projets --}}
        <div class="section">
            <div class="section-header">
                <h2>Mes projets</h2>
                <a href="{{ route('projects.create') }}" class="btn btn-secondary">
                    <span>+</span> Nouveau projet
                </a>
            </div>
            
            @if($myProjects->count() > 0)
                <div class="projects-grid">
                    @foreach($myProjects as $project)
                        <div class="project-card">
                            <div class="project-header">
                                <h3>
                                    <a href="{{ route('projects.show', $project) }}">
                                        {{ $project->name }}
                                    </a>
                                </h3>
                                <span class="badge badge-status">{{ $project->status }}</span>
                            </div>
                            
                            @if($project->description)
                                <p class="project-description">
                                    {{ Str::limit($project->description, 100) }}
                                </p>
                            @endif
                            
                            <div class="project-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $project->progress_percent }}%"></div>
                                </div>
                                <span class="progress-text">{{ $project->progress_percent }}%</span>
                            </div>
                            
                            <div class="project-stats">
                                <div class="stat">
                                    <span class="label">Tickets</span>
                                    <span class="value">{{ $project->tickets->count() }}</span>
                                </div>
                                <div class="stat">
                                    <span class="label">Équipe</span>
                                    <span class="value">{{ $project->teamMembers->count() }}</span>
                                </div>
                                @if($project->estimated_time)
                                    <div class="stat">
                                        <span class="label">Temps</span>
                                        <span class="value">{{ $project->spent_time }}h / {{ $project->estimated_time }}h</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="empty-state">Vous n'êtes membre d'aucun projet.</p>
            @endif
        </div>

        {{-- Tickets récents --}}
        <div class="section">
            <div class="section-header">
                <h2>Tickets récents</h2>
                <a href="{{ route('tickets.index') }}" class="link">Voir tous →</a>
            </div>
            
            @if($recentTickets->count() > 0)
                <div class="table-responsive">
                    <table class="tickets-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Projet</th>
                                <th>Statut</th>
                                <th>Priorité</th>
                                <th>Assigné à</th>
                                <th>Temps</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}">
                                            {{ $ticket->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $ticket->project) }}">
                                            {{ $ticket->project->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="status status-{{ strtolower(str_replace(' ', '-', $ticket->status)) }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($ticket->priority) }}">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ticket->workers->count() > 0)
                                            {{ $ticket->workers->pluck('first_name')->join(', ') }}
                                        @else
                                            <span class="text-muted">Non assigné</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->estimated_time)
                                            {{ $ticket->spent_time }}h / {{ $ticket->estimated_time }}h
                                        @else
                                            {{ $ticket->spent_time }}h
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="empty-state">Aucun ticket récent.</p>
            @endif
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    margin-bottom: 2rem;
}

.dashboard-header h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card.active {
    border-color: #3b82f6;
    background: #eff6ff;
}

.stat-icon {
    font-size: 2rem;
}

.stat-content h3 {
    font-size: 2rem;
    margin: 0;
    color: #1f2937;
}

.stat-content p {
    margin: 0;
    color: #6b7280;
}

.section {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header h2 {
    font-size: 1.5rem;
    margin: 0;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.project-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1rem;
}

.project-description {
    color: #6b7280;
    margin-bottom: 1rem;
}

.progress-bar {
    background: #e5e7eb;
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.progress-fill {
    background: #3b82f6;
    height: 100%;
    transition: width 0.3s;
}

.project-stats {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.project-stats .stat {
    display: flex;
    flex-direction: column;
}

.project-stats .label {
    font-size: 0.875rem;
    color: #6b7280;
}

.project-stats .value {
    font-weight: 600;
}

.tickets-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.ticket-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
}

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 0.5rem;
}

.ticket-meta {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
}

.badge-high { background: #fee2e2; color: #991b1b; }
.badge-medium { background: #fef3c7; color: #92400e; }
.badge-low { background: #dbeafe; color: #1e40af; }
.badge-status { background: #e5e7eb; color: #374151; }

.status {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
}

.status-new { background: #dbeafe; color: #1e40af; }
.status-in-progress { background: #fef3c7; color: #92400e; }
.status-on-hold { background: #fee2e2; color: #991b1b; }
.status-completed { background: #d1fae5; color: #065f46; }
.status-closed { background: #e5e7eb; color: #374151; }

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-secondary {
    background: white;
    color: #3b82f6;
    border: 1px solid #3b82f6;
}

.btn-secondary:hover {
    background: #eff6ff;
}

.table-responsive {
    overflow-x: auto;
}

.tickets-table {
    width: 100%;
    border-collapse: collapse;
}

.tickets-table th,
.tickets-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.tickets-table th {
    background: #f9fafb;
    font-weight: 600;
}

.tickets-table a {
    color: #3b82f6;
    text-decoration: none;
}

.tickets-table a:hover {
    text-decoration: underline;
}
</style>
@endsection
