@extends('layout.main')

@section('title')
    <title>Ticket details - Ticketing App</title>
@endsection

@section('resources')
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>Ticket #1: Customizable UI bars</h1>
        </header>

        <div class="detail-container">
            <section class="detail-card">
                <div class="detail-item">
                    <label>Title</label>
                    <h2>Customizable UI bars</h2>
                </div>

                <div class="detail-item" >
                    <label>Associated project</label>
                    <p>Skyblocker</p>
                </div>

                <div class="detail-item" >
                    <label>Detailed Description</label>
                    <p>Create modulable and customizable bars to replace the default bars of Hypixel Skyblock</p>
                </div>

                <div class="inline-elements">
                    <div class="detail-item">
                        <label>Actual Time Spent</label>
                        <p style="text-align: center" >4.50 hours</p>
                    </div>
                    <div class="detail-item">
                        <label>Estimated Time</label>
                        <p style="text-align: center" >8 hours</p>
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
                        <span class="badge green">In Progress</span>
                    </div>
                    <div class="detail-item">
                        <label>Priority</label>
                        <span class="badge orange">Medium</span>
                    </div>
                    <div class="detail-item">
                        <label>Type</label>
                        <span class="badge green">Included</span>
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Assigned Collaborators</h2>
                    <div id="collaborator-list">
                        <div class="user-profile-inline" style="margin-bottom: var(--spacing-sm);">
                            <img src="{{ asset("utils/images/icon.png") }}" alt="User Profile" class="profile-pic" >
                            <div class="item-stacked" style="margin-left: var(--spacing-sm);">
                                <div>
                                    <span class="username" data-type="first-name">Vic</span>
                                    <span class="username" data-type="last-name">IsACat</span>
                                </div>
                                <span class="user-role">Ticket Creator</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="detail-card full-width" id="file-list">
                <h2>Files associated</h2>
                <button class="btn" style="margin-bottom: var(--spacing-sm)">Edit documents</button>
                <ul>
                    <li>Visual Examples</li>
                    <li>Visual Examples</li>
                </ul>
            </div>
        </div>
    </main>
@endsection
