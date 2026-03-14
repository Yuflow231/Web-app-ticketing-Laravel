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

                <div class="detail-item">
                    <label>Detailed Description</label>
                    <p id="project-description">
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
                    <button class="btn">Edit Project</button>
                    <button class="btn btn--danger">Close Project</button>
                </div>
            </section>

            <div class="detail-side">
                <section class="detail-card">
                    <h2>Project Team</h2>
                    <div id="collaborator-list">
                        @foreach($project->teamMembers as $member)
                            <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm);" >
                                <img src="{{ $member->profile_pic ? asset('assets/images/' . $member->profile_pic) : asset('assets/images/icon.png') }}" alt="User Profile" class="profile-pic" >
                                <div class="item-stacked" style="margin-left: var(--spacing-sm);">
                                    <div>
                                        <span class="username" data-type="first-name">{{ $member->first_name }}</span>
                                        <span class="username" data-type="last-name">{{ $member->last_name }}</span>
                                    </div>
                                    <span class="user-role">{{ $member->pivot->role }}</span>
                                </div>
                            </div>
                        @endforeach
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
                            <th style="width: 40%">Ticket Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Type</th>
                            <th>Action</th>
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
                                <td data-label="Action"><a href="{{ route("tickets.ticket-details", $ticket->id) }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                            </tr>
                        @endforeach

                        <tr>
                            <td data-label="ID">#3</td>
                            <td data-label="Title" class="text-cell"><strong>Implement Dark Mode</strong></td>
                            <td data-label="Status"><span class="badge blue">New</span></td>
                            <td data-label="Priority"><span class="badge green">Low</span></td>
                            <td data-label="Type"><span class="badge red">Billed</span></td>
                            <td data-label="Action"><a href="{{ route("tickets.ticket-details", 1) }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="detail-card full-width">
                <h2>Files associated</h2>
                <button class="btn" style="margin-bottom: var(--spacing-sm)">Edit documents</button>
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
                            <p class="file-name">No files associated</p>
                        </li>
                    @endif

                </ul>
            </div>
        </div>
    </main>
@endsection


@section('js_page')
    <script type="module">
        import { TableManager } from "{{ asset("utils/js/table-handler.js") }}";

        // Initialize for the linked tickets table (using correct selector)
        new TableManager('#table', 5);
    </script>
@endsection
