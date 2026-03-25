@extends('layout.main')

@section('title')
    <title>Project details - Ticketing App</title>
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
            <h1 id="project-title-header">Project: {{ $project->name }}</h1>
        </header>

        <div class="detail-container" id="project-data-container">
            <section class="detail-card">
                <div class="detail-item">
                    <label>ID</label>
                    <p id="project-id">
                        {{ $project->id }}
                    </p>
                </div>
                <div class="inline-elements">
                    <div class="detail-item" style="text-align: center;">
                        <label>Status</label>
                        <span class="badge @php setBadgeColor($project->status) @endphp" style="text-align: center;">{{ $project->status }}</span>
                    </div>
                    @if($project->closing_date)
                        <div class="detail-item" style="text-align: center;">
                            <label>Closing date</label>
                            <p id="closing-date">
                                {{ optional($project->closing_date)->format("Y-m-d") }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="detail-item" style="max-width: 30rem;">
                    <label>Detailed Description</label>
                    <p id="project-description" style="word-wrap: break-word;">
                        {{ $project->description }}
                    </p>
                </div>

                <div class="inline-elements" style="margin-top: 2rem;">
                    <div class="detail-item" style="text-align: center;">
                        <label>Time Spent</label>
                        <p id="time-spent">{{ $project->spent_time }} hours</p>
                    </div>
                    <div class="detail-item" style="text-align: center;">
                        <label>Estimated Time</label>
                        <p id="estimated-time">{{ $project->estimated_time }} hours</p>
                    </div>
                </div>


                <div class="inline-elements" style="margin-top: auto; padding-top: 1rem;">
                    <button class="btn" onclick="location.href = '{{ route('projects.project-edit', $project->id) }}' ">Edit Project</button>
                </div>
            </section>

            <div class="detail-side">
                <section class="detail-card">
                    <h2>Project Team</h2>
                    <div id="collaborator-list">
                        @foreach($project->teamMembers->take(3) as $member)
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm);">
                                    <img src="{{ $member->profile_pic ? Storage::url($member->profile_pic) : asset('assets/images/icon.png') }}" alt="User Profile" class="profile-pic">
                                    <div class="item-stacked" style="margin-left: var(--spacing-sm);">
                                        <div>
                                            <span class="username">{{ $member->full_name }}</span>
                                        </div>
                                        <span class="user-role">{{ $member->pivot->role }}</span>
                                    </div>
                                </div>
                                <span class="time_spent">{{ $project->calculateUserSpentTime($member->id) }} hour(s)</span>
                            </div>
                        @endforeach

                        @if($project->teamMembers->count() > 3)
                            <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm); cursor: pointer; background: #f3f4f6; border-radius: var(--radius-md); padding: var(--spacing-sm); transition: all 0.2s;" onclick="document.getElementById('team-display').showModal()">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: var(--font-size-sm);">
                                    +{{ $project->teamMembers->count() - 3 }}
                                </div>
                                <div class="item-stacked" style="margin-left: var(--spacing-sm); flex: 1;">
                                    <span class="user-role" style="color: var(--text-secondary); font-size: var(--font-size-sm);">Click to view all {{ $project->teamMembers->count() }} team members</span>
                                </div>
                                <i class="fa-solid fa-chevron-right" style="color: var(--text-secondary); margin-left: auto;"></i>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Statistics</h2>
                    <div class="detail-item">
                        <label>Completion</label>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $project->progress_percent }}%;"></div>
                        </div>
                        <p style="font-size: var(--font-size-sm); margin-top: var(--spacing-sm);">{{ $project->progress_percent }}% of ticket completion</p>
                    </div>
                </section>
            </div>
            <section class="detail-card full-width">
                <h2>Linked Tickets</h2>
                <div class="page-header-line ">
                    <div class="filter">
                        <label for="type">Type</label>
                        <select id="type" name="type">
                            <option value="All">All</option>
                            <option value="Included">Included</option>
                            <option value="Billed">Billed</option>
                        </select>
                    </div>
                    <div class="filter">
                        <label for="search">Research</label>
                        <input type="text" id="search" name="search" placeholder="Research a project" value="{{ request('search', '') }}">
                    </div>
                </div>
                <div style="margin: var(--spacing-sm) 0;">
                    <button class="btn"><i class="fa-solid fa-angle-left"></i></button>
                    <span>Page 1 of 1</span>
                    <button class="btn"><i class="fa-solid fa-angle-right"></i></button>
                </div>
                <div class="table-card" style="margin-top: 0.5rem;">
                    <table id="table" style="width: 100%; font-size: 0.9rem;">
                        <thead>
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th style="width: 35%">Ticket Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Type</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($project->tickets as $ticket)
                            <tr>
                                <td data-label="ID">#{{ $ticket->id }}</td>
                                <td data-label="Title" class="text-cell"><strong>{{ $ticket->name }}</strong></td>
                                <td data-label="Status"><span class="badge @php setBadgeColor($ticket->status) @endphp">{{ $ticket->status }}</span></td>
                                <td data-label="Priority"><span class="badge @php setBadgeColor($ticket->priority) @endphp">{{ $ticket->priority }}</span></td>
                                <td data-label="Type"><span class="badge @php setBadgeColor($ticket->type) @endphp">{{ $ticket->type }}</span></td>
                                <td data-label="Action">
                                    <div style="display: flex; justify-content: center;">
                                        <a href="{{ route("tickets.ticket-details", $ticket->id) }}" class="icon" style="font-size: var(--font-size-xl);"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="detail-card full-width">
                <h2>Associated contract</h2>
                <ul id="file-list"  style="list-style-type: none;">
                    @if($project->contract)
                        <li>
                            <p class="file-name"> {{ basename($project->contract) }} </p>
                            <div style="color: var(--primary-color); flex-shrink: 0;">
                                <a href="{{ Storage::url($project->contract) }}" target="_blank" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                <a href="{{ Storage::url($project->contract) }}" download="{{ basename($project->contract) }}" class="icon"><i class="fa-solid fa-download"></i></a>
                            </div>
                        </li>
                    @else
                        <li>
                            <p class="file-name">No contract associated</p>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </main>
@endsection

@section('modal')
    @if($project->teamMembers->count() > 3)
    {{-- Modal to display the entire team --}}
        <dialog id="team-display" class="modal-container">
            <div style="display: flex; justify-content: space-between">
                <h2 style="margin-bottom: 0.5rem;">Project's team</h2>
                <button type="button" class="icon" onclick="document.getElementById('team-display').close()" style="font-size: 1.5rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Search bar --}}
            <div class="form-item-stacked">
                <input type="text" id="team-member-search" placeholder="Search by name or email...">
            </div>

            {{-- User list --}}
            <div id="team-list" class="modal-list">
                @foreach($project->teamMembers as $member)
                    <div class="modal-list-selectable team-member-list" data-user-name="{{ $member->full_name }}" data-user-email="{{ $member->email }}">
                        <img src="{{ $member->profile_pic ? Storage::url($member->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                        <div style="flex: 1;">
                            <div class="modal-list-name">{{ $member->full_name }}</div>
                            <div class="modal-list-subname">{{ $member->email }}</div>
                        </div>
                        <div>
                            <span class="time_spent">{{ $project->calculateUserSpentTime($member->id) }} hour(s)</span>
                            @if($member->isAdmin())
                                <span class="badge blue">Admin</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 1rem; display: flex; justify-content: center;">
                <button type="button" class="btn btn--outline" onclick="document.getElementById('team-display').close()">
                    Cancel
                </button>
            </div>
        </dialog>
    @endif
@endsection

@section('js_page')
    <script type="module">
        import { TableManager } from "{{ asset("utils/js/table-handler.js") }}";

        // Initialize for the linked tickets table (using correct selector)
        new TableManager('#table', 5);

        @if($project->teamMembers()->count() > 3)
        // Team Search
        const teamSearch = document.getElementById('team-member-search');
        const teamItems = document.querySelectorAll('.team-member-list');

        teamSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            teamItems.forEach(item => {
                const name = item.dataset.userName.toLowerCase();
                const email = item.dataset.userEmail.toLowerCase();

                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        @endif
    </script>
@endsection
