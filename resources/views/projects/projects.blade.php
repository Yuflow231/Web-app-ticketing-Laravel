@extends('layout.main')

@section('title')
    <title>Projects - Ticketing App</title>
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
                        <th>ID</th>
                        <th>Project name</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Creation date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Projects will be loaded here -->
                    <!-- Project template -->
                    <tr>
                        <td data-label="ID">#1</td>
                        <td data-label="Project name"><strong>Skyblocker</strong></td>
                        <td data-label="Client">
                            <div class="user-profile-inline">
                                <img src="{{ asset("utils/images/icon.png") }}" class="profile-pic" alt="profile-picture" style="width:40px; height:40px;">
                                <span style="margin-left: var(--spacing-sm)">VicIsACat</span>
                            </div>
                        </td>
                        <td data-label="Status">
                            <span class="badge green">In Progress</span>
                        </td>
                        <td data-label="Progress">
                            <div class="progress-container">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 3%;"></div>
                                </div>
                                <div class="progress-percentage">3%</div>
                            </div>
                        </td>
                        <td data-label="Creation date">1996-02-02</td>
                        <td data-label="Actions">
                            <div style="display: flex; justify-content: space-evenly">
                                <a href="{{ route('projects.project-details') }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                <button type="submit" class="icon" style="color: var(--danger-color); background: none; border: none; cursor: pointer;"> <i class="fa-solid fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td data-label="ID">#2</td>
                        <td data-label="Project name"><strong>Skyblocker</strong></td>
                        <td data-label="Client">
                            <div class="user-profile-inline">
                                <img src="{{ asset("utils/images/icon.png") }}" class="profile-pic" alt="profile-picture" style="width:40px; height:40px;">
                                <span style="margin-left: var(--spacing-sm)">VicIsACat</span>
                            </div>
                        </td>
                        <td data-label="Status">
                            <span class="badge green">In Progress</span>
                        </td>
                        <td data-label="Progress">
                            <div class="progress-container">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 3%;"></div>
                                </div>
                                <div class="progress-percentage">3%</div>
                            </div>
                        </td>
                        <td data-label="Creation date">1996-02-02</td>
                        <td data-label="Actions">
                            <div style="display: flex; justify-content: space-evenly">
                                <a href="{{ route('projects.project-details') }}" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                <button type="submit" class="icon" style="color: var(--danger-color); background: none; border: none; cursor: pointer;"> <i class="fa-solid fa-trash"></i></button>
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

        // Initialize for projects table
        new TableManager('#table', 5);
    </script>
@endsection
