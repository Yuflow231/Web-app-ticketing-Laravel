@extends('layout.main')

@section('title')
    <title>Dashboard - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
    @php {{ require_once public_path("utils/php/badge-color-assigner.php"); }} @endphp
    @php use Illuminate\Support\Facades\Storage; @endphp
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Dashboard</h1>
        </header>
        <!-- Statistics Section -->
        <section id="statistics">
            <h2>Statistics</h2>
            <div class="grid-stats">
                <div class="stat-card">
                    <div class="icon"><i class="fa-solid fa-diagram-project"></i></div>
                    <div class="stat-details">
                        <h3 id="stat-projects">{{ $stats["total_projects"] }}</h3>
                        <p class="text">Totals projects</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fa-solid fa-ticket"></i></div>
                    <div class="stat-details">
                        <h3 id="stat-tickets">{{ $stats["active_projects"] }}</h3>
                        <p class="text">Active Projects</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="stat-details">
                        <h3 id="stat-urgent">{{ $stats["total_tickets"] }}</h3>
                        <p class="text">Total Tickets</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-details">
                        <h3 id="stat-closed">{{ $stats["active_tickets"] }}</h3>
                        <p class="text">Active Tickets</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Projects Section -->
        <section id="projects">
            <h2>Recent projects</h2>
            <div class="grid-content" id="recent-projects">
                <!-- Projects will be loaded here -->
                @foreach($recentProjects as $project)
                    <div class="card flex-column">
                        <h3>{{ $project->name }}</h3>
                        <p>Tickets: {{ $project->tickets->count() }} </p>
                        <p>Status:  <span class="badge @php setBadgeColor($project->status) @endphp"> {{ $project->status }} </span></p>
                        <button type="button" class="btn btn--outline" style="margin-top: 0.5rem;" onclick="location.href = '{{ route('projects.project-details', $project->id) }}'">Quick access</button>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Recent Tickets Section -->
        <section class="tickets">
            <h2>Recent tickets</h2>
            <div class="table-card">
                <table>
                    <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Assigned</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="recent-tickets">

                    <!-- Tickets will be loaded here -->
                    @foreach($recentTickets as $ticket)
                    <tr>
                        <td data-label="ID">#{{$ticket->id}}</td>
                        <td data-label="Title" class="text-cell"><strong>{{$ticket->name}}</strong></td>
                        <td data-label="Project" class="text-cell">{{$ticket->project->name}}</td>
                        <td data-label="Status"><span class="badge @php setBadgeColor($ticket->status) @endphp">{{$ticket->status}}</span></td>
                        <td data-label="Priority"><span class="badge @php setBadgeColor($ticket->priority) @endphp">{{$ticket->priority}}</span></td>
                        <td data-label="Assigned">
                            <div class="avatar-line">
                                @foreach($ticket->workers->take(3) as $worker)
                                    <img src="{{ !empty($worker->profile_pic) ? Storage::url($worker->profile_pic) : asset('assets/images/icon.png') }}" title="{{ $worker->full_name }}" alt="profile-picture" class="profile-pic-mini">
                                @endforeach
                                @if($ticket->workers->count() > 3)
                                    <span class="profile-pic-more" title="{{ $ticket->workers->count() - 3 }} more">...</span>
                                @endif
                            </div>
                        </td>
                        <td data-label="Actions"><a href="{{ route("tickets.ticket-details", $ticket->id) }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection
