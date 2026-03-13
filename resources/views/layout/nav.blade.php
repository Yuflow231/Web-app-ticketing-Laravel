@php
/**
 * Helper function to generate navigation links with proper active state and debug param
 * @param string $refLink The target URL
 */
function laink($route): void {
    $length = 5;
    $isActive = substr($route,0, $length) === substr(\Illuminate\Support\Facades\Route::currentRouteName(),0, $length);

    $activeClass = $isActive ? ' class="active"' : '';

    $refLink = route($route);
    echo "<a href='{$refLink}'{$activeClass}>";
}
@endphp


<nav class="navigation">
    <header class="top-bar">
        <div class="menu-bar">
            <span class="hamburger"><i class="fa-solid fa-bars"></i></span>
            <span>| Ticketing App</span>
        </div>
        <div class="user-profile-header">
            <a href="{{ route("profile") }}" class="user-profile-inline">
                <span class="username" data-type="first-name"> {{ auth()->user()->first_name }} </span>
                <span class="username" data-type="last-name"> {{ auth()->user()->last_name }} </span>

                <!-- <img src="{{ asset("assets/images/yuflow.jpg") }}" alt="User Profile" class="profile-pic" > -->
                <img src="{{ !empty(auth()->user()->profile_pic) ? asset('assets/images/'.auth()->user()->profile_pic) : asset('assets/images/icon.png') }}" alt="User Profile" class="profile-pic">
            </a>
        </div>
    </header>

    <!-- Side Navigation Bar -->
    <div class="side-nav">
        <div class="top-side">
            @php laink('dashboard')@endphp
            <span class="icon"><i class="fa-solid fa-chart-line"></i></span>
            <span class="text">Dashboard</span>
            </a>
            @php laink('projects.projects') @endphp
            <span class="icon"><i class="fa-solid fa-diagram-project"></i></span>
            <span class="text">Projects</span>
            </a>
            @php laink('tickets.tickets')@endphp
            <span class="icon"><i class="fa-solid fa-ticket"></i></span>
            <span class="text">Tickets</span>
            </a>
            @php laink('profile')@endphp
            <span class="icon"><i class="fa-solid fa-user"></i></span>
            <span class="text">Profile</span>
            </a>
        </div>

        <form id="logout" method="post" action="{{ route("logout") }}">
        @csrf
            <a href="javascript:{}" onclick="document.getElementById('logout').submit();">
                <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                <span class="text">Logout</span>
            </a>
        </form>
</div>
</nav>
