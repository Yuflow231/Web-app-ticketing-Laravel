@extends('layout.main')

@section('title')
    <title>Tickets - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
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
                        <th>ID</th>
                        <th>Title</th>
                        <th>Project</th>
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
                    <tr>
                        <td data-label="ID">#1</td>
                        <td data-label="Title"><strong> Customizable UI bars</strong></td>
                        <td data-label="Project">Skyblocker</td>
                        <td data-label="Status"><span class="badge green">In Progress</span></td>
                        <td data-label="Priority"><span class="badge orange">Medium</span></td>
                        <td data-label="Type"><span class="badge green">Included</span></td>
                        <td data-label="Assigned">
                            <div class="avatar-line">
                                <img src="{{ asset("utils/images/icon.png") }}" title="Vic IsACat" alt="profile_pic" class="profile-pic-mini">
                            </div>
                        </td>
                        <td data-label="Actions">
                            <div style="display: flex; justify-content: space-evenly">
                                <a href="{{ route("tickets.ticket-details") }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>

                                <button type="submit" class="icon" style="color: var(--danger-color); background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td data-label="ID">#3</td>
                        <td data-label="Title"><strong>Implement Dark Mode</strong></td>
                        <td data-label="Project">Skyblocker</td>
                        <td data-label="Status"><span class="badge blue">New</span></td>
                        <td data-label="Priority"><span class="badge green">Low</span></td>
                        <td data-label="Type"><span class="badge red">Billed</span></td>
                        <td data-label="Assigned">
                            <div class="avatar-line">
                                <img src="{{ asset("utils/images/icon.png") }}" title="Vic IsACat" alt="profile_pic" class="profile-pic-mini">
                            </div>
                        </td>
                        <td data-label="Actions">
                            <div style="display: flex; justify-content: space-evenly">
                                <a href="{{ route("tickets.ticket-details") }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>

                                <button type="submit" class="icon" style="color: var(--danger-color); background: none; border: none; cursor: pointer;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
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

@section('js_page')
    <script type="module">
        import { TableManager } from "{{ asset("utils/js/table-handler.js") }}";

        // Initialize for tickets table
        new TableManager('#table', 5);
    </script>
@endsection
