@extends('layout.main')

@section('title')
    <title>Tickets - Ticketing App</title>
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
            <div class="page-header-line">
                <h2>My tickets</h2>
                <a href="{{ route("tickets.ticket-creation") }}">
                    <button type="button" class="btn">
                        <i class="fa-solid fa-plus"></i>
                        Create tickets
                    </button>
                </a>
            </div>
            <div class="page-header-line">
                <div style="display: flex; gap: var(--spacing-lg); flex-wrap: wrap;">
                    <div class="filter">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="All">All</option>
                            <option value="New">New</option>
                            <option value="In Progress">In Progress</option>
                            <option value="On Hold">On Hold</option>
                            <option value="Completed">Completed</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <div class="filter">
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority">
                            <option value="All">All</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                    <div class="filter">
                        <label for="type">Type</label>
                        <select id="type" name="type">
                            <option value="All">All</option>
                            <option value="Included">Included</option>
                            <option value="Billed">Billed</option>
                        </select>
                    </div>
                </div>
                <div class="filter">
                    <label for="search">Research</label>
                    <input type="text" id="search" name="search" placeholder="Research a project">
                </div>
            </div>
            <div>
                <button class="btn"><i class="fa-solid fa-angle-left"></i></button>
                <span>Page 1 of 1</span>
                <button class="btn"><i class="fa-solid fa-angle-right"></i></button>
            </div>
        </header>

        <!-- Projects section -->
        <section class="project">
            <div class="table-card">
                <table id="table">
                    <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 15%">Title</th>
                        <th style="width: 15%">Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Type</th>
                        <th>Assigned</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Tickets will be loaded here -->
                    <!-- Ticket template -->
                    @foreach($tickets->items() as $ticket)
                    <tr>
                        <td data-label="ID">#{{ $ticket->id }}</td>
                        <td data-label="Title" class="text-cell"><strong>{{ $ticket->name }}</strong></td>
                        <td data-label="Project" class="text-cell">{{$ticket->project->name}}</td>
                        <td data-label="Status"><span class="badge @php setBadgeColor($ticket->status) @endphp">{{ $ticket->status }}</span></td>
                        <td data-label="Priority"><span class="badge @php setBadgeColor($ticket->priority) @endphp">{{ $ticket->priority }}</span></td>
                        <td data-label="Type"><span class="badge @php setBadgeColor($ticket->type) @endphp">{{ $ticket->type }}</span></td>
                        <td data-label="Assigned">
                            <div class="avatar-line">
                                @foreach($ticket->workers as $worker)
                                    <img src="{{ !empty($worker->profile_pic) ? Storage::url($worker->profile_pic) : asset('assets/images/icon.png') }}" title="{{ $worker->first_name. ' ' .$worker->last_name }}" alt="profile-picture" class="profile-pic-mini">
                                @endforeach
                            </div>
                        </td>
                        <td data-label="Actions">
                            <div style="display: flex; justify-content: space-evenly">
                                <a href="{{ route("tickets.ticket-details", $ticket->id) }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>

                                <button type="button" class="icon btn-delete-project"
                                        data-ticket-id="{{ $ticket->id }}"
                                        data-ticket-name="{{ $ticket->name }}"
                                        style="color: var(--danger-color); background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="page-footer">
            <div>
                <button class="btn"><i class="fa-solid fa-angle-left"></i></button>
                <span>Page 1 of 1</span>
                <button class="btn"><i class="fa-solid fa-angle-right"></i></button>
            </div>
        </footer>
    </main>
@endsection

@section('modal')
    {{-- Delete ticket modal --}}
    <dialog id="delete-modal" class="modal-container" style="width: 30%;">
        <h2>Delete ticket</h2>
        <p style="margin-bottom: 1rem; color: var(--text-secondary);"> You are about to delete <br> <strong id="modal-project-name"></strong> <br> This action is irreversible. </p>
        <form method="POST" action="#" id="delete-form">
            @csrf
            @method('DELETE')
            <div class="inline-elements">
                <button type="button" class="btn btn--outline" onclick="document.getElementById('delete-modal').close()">Cancel</button>
                <button type="submit" class="btn btn--danger">Confirm deletion</button>
            </div>
        </form>
    </dialog>
@endsection

@section('js_page')
    <script type="module">
        import { TableManager } from "{{ asset("utils/js/table-handler.js") }}";
        new TableManager('#table', 5);

        const modal     = document.getElementById('delete-modal');
        const form      = document.getElementById('delete-form');
        const modalName = document.getElementById('modal-project-name');

        document.querySelectorAll('.btn-delete-project').forEach(btn => {
            btn.addEventListener('click', () => {
                modalName.textContent = btn.dataset.ticketName;
                form.action = "{{ route('tickets.ticket-destroy', '__ID__') }}".replace('__ID__', btn.dataset.ticketId);
                modal.showModal();
            });
        });
    </script>
@endsection
