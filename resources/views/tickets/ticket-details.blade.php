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

                <div class="detail-item" style="max-width: 30rem;">
                    <label>Detailed Description</label>
                    <p style="word-wrap: break-word;">{{ $ticket->description }}</p>
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
                    <button class="btn" onclick="location.href = '{{ route("tickets.ticket-edit", $ticket->id) }}' ">Edit Ticket</button>
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
                        @foreach($ticket->workers->take(3) as $worker)
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm);" >
                                    <img src="{{ !empty($worker->profile_pic) ? Storage::url($worker->profile_pic) : asset('assets/images/icon.png') }}" alt="User Profile" class="profile-pic" >
                                    <div class="item-stacked" style="margin-left: var(--spacing-sm);">
                                        <div>
                                            <span class="username">{{ $worker->full_name }}</span>
                                        </div>
                                        <span class="user-role">{{ $worker->pivot->role ? $worker->pivot->role : "No role" }}</span>
                                    </div>
                                </div>
                                <span class="time_spent">{{ $worker->pivot->spent_time }} hour(s)</span>
                            </div>
                        @endforeach
                        @if($ticket->workers->count() > 3)
                                <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm); cursor: pointer; background: #f3f4f6; border-radius: var(--radius-md); padding: var(--spacing-sm); transition: all 0.2s;" onclick="document.getElementById('team-display').showModal()">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: var(--font-size-sm);">+{{ $ticket->workers->count() - 3 }}</div>
                                    <div class="item-stacked" style="margin-left: var(--spacing-sm); flex: 1;">
                                        <span class="user-role" style="color: var(--text-secondary); font-size: var(--font-size-sm);">Click to view all {{ $ticket->workers->count() }} team members</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-right" style="color: var(--text-secondary); margin-left: auto;"></i>
                                </div>
                        @endif
                    </div>
                </section>
            </div>

            <div class="detail-card full-width">
                <h2>Files associated</h2>
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


@section('modal')
    @if($ticket->workers->count() > 3)
    {{-- Modal to display the entire team --}}
        <dialog id="team-display" class="modal-container">
            <div style="display: flex; justify-content: space-between">
                <h2 style="margin-bottom: 0.5rem;">Ticket's team</h2>
                <button type="button" class="icon" onclick="document.getElementById('team-display').close()" style="font-size: 1.5rem; color: var(--text-secondary);">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Search bar --}}
            <div class="form-item-stacked">
                <input type="text" id="team-member-search" placeholder="Search by name or role...">
            </div>

            {{-- User list --}}
            <div id="team-list" class="modal-list">
                @foreach($ticket->workers as $member)
                    <div class="modal-list-selectable team-member-list" data-user-name="{{ $member->full_name }}" data-user-role="{{ $member->pivot->role }}">
                        <img src="{{ $member->profile_pic ? Storage::url($member->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic-mini" alt="profile-pic">
                        <div style="flex: 1;">
                            <div class="modal-list-name">{{ $member->full_name }}</div>
                            <div class="modal-list-subname">{{ $member->pivot->role ? $member->pivot->role : "No role assigned" }}</div>
                        </div>
                        <div>
                            <span class="time_spent">{{ $member->pivot->spent_time }} hour(s)</span>
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
    @if($ticket->workers->count() > 3)
    <script type="module">
        // Team Search
        const teamSearch = document.getElementById('team-member-search');
        const teamItems = document.querySelectorAll('.team-member-list');

        teamSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            teamItems.forEach(item => {
                const name = item.dataset.userName.toLowerCase();
                const role = item.dataset.userRole.toLowerCase();

                if (name.includes(searchTerm) || role.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
    @endif
@endsection
