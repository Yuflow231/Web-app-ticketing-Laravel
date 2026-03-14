@extends('layout.main')

@section('title')
    <title>Projects - Ticketing App</title>
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
                <h2>My projects</h2>
                <a href="{{ route("projects.project-creation") }}">
                    <button type="button" class="btn">
                        <i class="fa-solid fa-plus"></i>
                        Create project
                    </button>
                </a>
            </div>
            <div class="page-header-line">
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
                    <label for="search">Research</label>
                    <input type="text" id="search" name="search" placeholder="Research a project" value="{{ request('search', '') }}">
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
                        <th style="width: 20%">Project name</th>
                        <th  style="width: 20%">Owner</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Creation date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Projects will be loaded here -->
                    <!-- Project template -->
                    @foreach($projects->items() as $project)
                        @php $owner = $project->owner->first(); @endphp
                        <tr>
                            <td data-label="ID">#{{ $project->id }}</td>
                            <td data-label="Project name" class="text-cell"><strong>{{ $project->name }}</strong></td>
                            <td data-label="Client" class="text-cell">
                                <div class="user-profile-inline">
                                    <img src="{{ $owner->profile_pic ? Storage::url($owner->profile_pic) : asset('assets/images/icon.png') }}" class="profile-pic" alt="profile-picture" style="width:40px; height:40px;">
                                    <span style="margin-left: var(--spacing-sm)">{{ $owner->first_name.' '.$owner->last_name}}</span>
                                </div>
                            </td>
                            <td data-label="Status">
                                <span class="badge @php setBadgeColor($project->status) @endphp">{{ $project->status }}</span>
                            </td>
                            <td data-label="Progress">
                                <div class="progress-container">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $project->progress_percent }}%;"></div>
                                    </div>
                                    <div class="progress-percentage">{{ $project->progress_percent }}%</div>
                                </div>
                            </td>
                            <td data-label="Creation date">{{ optional($project->created_at)->format('Y-m-d') }}</td>
                            <td data-label="Actions">
                                <div style="display: flex; justify-content: space-evenly">
                                    <a href="{{ route('projects.project-details', $project->id) }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                    <button type="button" class="icon btn-delete-project"
                                            data-project-id="{{ $project->id }}"
                                            data-project-name="{{ $project->name }}"
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

        {{-- Delete project modal --}}
        <dialog id="delete-modal" class="modal-container">
            <h2>Delete project</h2>
            <p style="margin-bottom: 1rem; color: var(--text-secondary);">
                You are about to delete <br> <strong id="modal-project-name"></strong> <br> This action is irreversible.
            </p>

            <form method="POST" action="#" id="delete-form">
                @csrf
                @method('DELETE')
                <div class="inline-elements">
                    <button type="button" class="btn btn--outline" onclick="document.getElementById('delete-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn--danger">Confirm deletion</button>
                </div>
            </form>
        </dialog>

        <footer class="page-footer">
            <div>
                <button class="btn"><i class="fa-solid fa-angle-left"></i></button>
                <span>Page 1 of 1</span>
                <button class="btn"><i class="fa-solid fa-angle-right"></i></button>
            </div>
        </footer>
    </main>
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
                modalName.textContent = btn.dataset.projectName;
                form.action = "{{ route('projects.project-destroy', '__ID__') }}".replace('__ID__', btn.dataset.projectId);
                modal.showModal();
            });
        });
    </script>
@endsection
