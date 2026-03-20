@extends('layout.main')

@section('title')
    <title>Profile - Ticketing App</title>
@endsection

@section('resources')
    @php use Illuminate\Support\Facades\Storage; @endphp
    <script src="{{ asset("utils/js/side-bar.js") }}" defer></script>
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
                        <div class="username" data-type="first-name">{{ auth()->user()->first_name }}</div>
                        <div class="username" data-type="last-name">{{ auth()->user()->last_name }}</div>
                        <p class="user-role">{{ auth()->user()->role }}</p>
                    </div>


                    <!-- <img src="{{ asset("assets/images/yuflow.jpg") }}" alt="User Profile" class="profile-pic" > -->
                    <img src="{{ !empty(auth()->user()->profile_pic) ? Storage::url(auth()->user()->profile_pic) : asset('assets/images/icon.png') }}" alt="User Profile" class="profile-pic">

                </header>

                <div>
                    <div class="detail-item">
                        <label>Email Address</label>
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Member Since</label>
                        <p>{{ optional(auth()->user()->created_at)->format("Y-m-d") }}</p>
                    </div>
                    <div style="display: flex; gap: var(--spacing-sm);">
                        <button onclick="location.href = '{{ route('profile.edit') }}'" type="button" class="btn">Edit</button>
                    </div>
                </div>
            </section>

            <div class="detail-side">
                <section class="detail-card">
                    <h2>Preferences</h2>
                    <div class="detail-item">
                        <label>Language</label>
                        <p>
                            @switch(auth()->user()->language ?? 'en')
                                @case('en')
                                    English
                                    @break
                                @case('fr')
                                    Français
                                    @break
                            @endswitch
                        </p>
                    </div>
                </section>

                <section class="detail-card">
                    <h2>Security</h2>
                    <div class="detail-item">
                        <label>Password</label>
                        <p style="margin-bottom: 1rem;">••••••••••••</p>
                        <a href="{{ route('reset-password') }}" class="password" style="">Change Password</a>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@section('modal')
@endsection

@section('js_page')
@endsection
