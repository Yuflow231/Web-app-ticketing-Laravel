@extends('layout.main')

@section('title')
    <title>Project details - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1 id="project-title-header">Project: Skyblocker</h1>
        </header>

        <div class="detail-container" id="project-data-container">
            <section class="detail-card">
                <div class="detail-item">
                    <label>ID</label>
                    <p id="project-id">
                        #1
                    </p>
                </div>
                <div class="inline-elements">
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="badge green">In Progress</span>
                    </div>
                    <div class="detail-item">
                        <label>Closing date</label>
                        <p id="closing-date">
                            2026-03-24
                        </p>
                    </div>
                </div>

                <div class="detail-item">
                    <label>Detailed Description</label>
                    <p id="project-description">
                        Create a minecraft mode that act as an add-on for the Hypixel server, more precisely for its Skyblock game mode. Its role is to enhance the game experience by providing quality of life improvement, as well as improving guidance.
                    </p>
                </div>

                <div class="inline-elements" style="margin-top: 2rem;">
                    <div class="detail-item">
                        <label>Actual Time Spent</label>
                        <p id="actual-time" style="text-align: center">120.00 hours</p>
                    </div>
                    <div class="detail-item">
                        <label>Estimated Time</label>
                        <p id="est-time" style="text-align: center">120.00 hours</p>
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
                        <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm);" >
                            <img src="{{ asset("utils/images/yuflow.jpg") }}" alt="User Profile" class="profile-pic" >
                            <div class="item-stacked" style="margin-left: var(--spacing-sm);">
                                <div>
                                    <span class="username" data-type="first-name">Yuflow2</span>
                                    <span class="username" data-type="last-name">Furry</span>
                                </div>
                                <span class="user-role">Maintainer</span>
                            </div>
                        </div>
                        <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm);" >
                            <img src="{{ asset("utils/images/icon.png") }}" alt="User Profile" class="profile-pic" >
                            <div class="item-stacked" style="margin-left: var(--spacing-sm);">
                                <div>
                                    <span class="username" data-type="first-name">Vic</span>
                                    <span class="username" data-type="last-name">IsACat</span>
                                </div>
                                <span class="user-role">Owner</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Statistics</h2>
                    <div class="detail-item">
                        <label>Completion</label>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 3%;"></div>
                        </div>
                        <p style="font-size: var(--font-size-sm); margin-top: var(--spacing-sm);">3% of ticket completion</p>
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
                            <th>ID</th>
                            <th>Ticket Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td data-label="ID">#1</td>
                            <td data-label="Title"><strong>Customizable UI bars</strong></td>
                            <td data-label="Status"><span class="badge green">In Progress</span></td>
                            <td data-label="Priority"><span class="badge orange">Medium</span></td>
                            <td data-label="Type"><span class="badge green">Included</span></td>
                            <td data-label="Action"><a href="#" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                        </tr>

                        <tr>
                            <td data-label="ID">#3</td>
                            <td data-label="Title"><strong>Implement Dark Mode</strong></td>
                            <td data-label="Status"><span class="badge blue">New</span></td>
                            <td data-label="Priority"><span class="badge green">Low</span></td>
                            <td data-label="Type"><span class="badge red">Billed</span></td>
                            <td data-label="Action"><a href="#" class="icon"><i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="detail-card full-width" id="file-list">
                <h2>Files associated</h2>
                <button class="btn" style="margin-bottom: var(--spacing-sm)">Edit documents</button>
                <ul>
                    <li>Business Contract</li>
                    <li>User stories</li>
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
