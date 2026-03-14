@extends('layout.main')

@section('title')
    <title>Ticket details - Ticketing App</title>
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
            <h1>Ticket #{{ $ticket->id }}: {{ $ticket->name }}</h1>
        </header>

        <div class="detail-container">
            <section class="detail-card">
                <div class="detail-item">
                    <label>Title</label>
                    <h2>{{ $ticket->name }}</h2>
                </div>

                <div class="detail-item">
                    <label>Associated project</label>
                    <p>{{ $ticket->project->name }}</p>
                </div>

                <div class="detail-item" >
                    <label>Detailed Description</label>
                    <p>{{ $ticket->description }}</p>
                </div>

                <div class="inline-elements">
                    <div class="detail-item" style="text-align: center;">
                        <label>Time Spent</label>
                        <p id="time-spent">{{ $ticket->spent_time }} hours</p>
                    </div>
                    <div class="detail-item" style="text-align: center;">
                        <label>Estimated Time</label>
                        <p id="estimated-time">{{ $ticket->estimated_time }} hours</p>
                    </div>
                </div>

                <div class="inline-elements" style="margin-top: auto; padding-top: 1rem;">
                    <button class="btn">Edit Ticket</button>
                    <button class="btn btn--danger">Close Ticket</button>
                </div>
            </section>

            <div class="detail-side">
                <section class="detail-card">
                    <h2>Classification</h2>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="badge @php setBadgeColor($ticket->status) @endphp">{{ $ticket->status }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Priority</label>
                        <span class="badge @php setBadgeColor($ticket->priority) @endphp">{{ $ticket->priority }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Type</label>
                        <span class="badge @php setBadgeColor($ticket->type) @endphp">{{ $ticket->type }}</span>
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Assigned Collaborators</h2>
                    <div id="collaborator-list">
                        @foreach($ticket->workers as $worker)
                            <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm);">
                                <img src="{{ !empty($worker->profile_pic) ? Storage::url($worker->profile_pic) : asset('assets/images/icon.png') }}" alt="User Profile" class="profile-pic" >
                                <div class="item-stacked" style="margin-left: var(--spacing-sm);">
                                    <div>
                                        <span class="username" data-type="first-name">{{ $worker->first_name }}</span>
                                        <span class="username" data-type="last-name">{{ $worker->last_name }}</span>
                                    </div>
                                    <span class="user-role">{{ $worker->pivot->role }}</span>
                                </div>
                            </div>
                        @endforeach

                        <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm);">
                            <img src="{{ asset("assets/images/icon.png") }}" alt="User Profile" class="profile-pic" >
                            <div class="item-stacked" style="margin-left: var(--spacing-sm);">
                                <div>
                                    <span class="username" data-type="first-name">Vic</span>
                                    <span class="username" data-type="last-name">IsACat</span>
                                </div>
                                <span class="user-role">Helper</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="detail-card full-width">
                <h2>Files associated</h2>
                <button class="btn" style="margin-bottom: var(--spacing-sm)">Edit documents</button>
                <ul id="file-list">
                    @if($ticket->attachments->isNotEmpty())
                        @foreach($ticket->attachments as $attachment)
                            <li>
                                <p class="file-name"> {{ basename($attachment->file_name) }}</p>
                                <div style="color: var(--primary-color); flex-shrink: 0;">
                                    <a href="{{ Storage::url($attachment->file_name) }}" target="_blank" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                    <a href="{{ Storage::url($attachment->file_name) }}" download="{{ basename($attachment->file_name) }}" class="icon"><i class="fa-solid fa-download"></i></a>
                                </div>
                            </li>
                        @endforeach
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
