@extends('layout.main')

@section('title')
    <title>Profile - Ticketing App</title>
@endsection

@section('resources')
@endsection

@section('content')
    @include('layout.nav')
    <!-- Main Content -->
    <main class="main-content">
        <header class="page-header">
            <h1>User Profile</h1>
        </header>

        <div class="detail-container">
            <section class="detail-card">
                <header class="profile-header">
                    <div class="name-group">
                        <div class="username" data-type="first-name">Yuflow</div>
                        <div class="username" data-type="last-name">Furry</div>
                        <p class="user-role">Administrator</p>
                    </div>


                    <img src="{{ asset("utils/images/yuflow.jpg") }}" alt="User Profile" class="profile-pic" >
                </header>

                <div>
                    <div class="detail-item">
                        <label>Email Address</label>
                        <p>Yuflow@Yuflow.com</p>
                    </div>
                    <div class="detail-item">
                        <label>Member Since</label>
                        <p>2026-03-08</p>
                    </div>
                    <div>
                        <button type="button" class="btn">Edit</button>
                    </div>
                </div>
            </section>

            <div class="detail-side">
                <section class="detail-card">
                    <h2>Preferences</h2>
                    <div class="form-item" style="width: 10rem;">
                        <label for="language-select">Language</label>
                        <select id="language-select">
                            <option value="en" selected>English</option>
                            <option value="fr">French</option>
                        </select>
                    </div>
                    <div class="form-item-stacked">
                        <label for="debug">Debug mode</label>
                        <input type="checkbox" id="debug">
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Security</h2>
                    <div class="detail-item">
                        <label>Password</label>
                        <p style="margin-bottom: 1rem;">••••••••••••</p>
                        <a href="{{ route('reset-password') }}>" class="password" style="">Change Password</a>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('js_page')
@endsection
