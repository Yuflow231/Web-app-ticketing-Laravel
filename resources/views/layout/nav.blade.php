<?php
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
?>

<nav class="navigation">
    <header class="top-bar">
        <div class="menu-bar">
            <span class="hamburger"><i class="fa-solid fa-bars"></i></span>
            <span>| Ticketing App</span>
        </div>
        <div class="user-profile-header">
            <a href="#" class="user-profile-inline">
                <span class="username" data-type="first-name">Yuflow</span>
                <span class="username" data-type="last-name">Furry</span>

                <img src="{{ asset("utils/images/yuflow.jpg") }}" alt="User Profile" class="profile-pic" >
            </a>
        </div>
    </header>

    <!-- Side Navigation Bar -->
    <div class="side-nav">
        <div class="top-side">
            <?php laink('dashboard')?>
            <span class="icon"><i class="fa-solid fa-chart-line"></i></span>
            <span class="text">Dashboard</span>
            </a>
            <?php laink('projects.projects')?>
            <span class="icon"><i class="fa-solid fa-diagram-project"></i></span>
            <span class="text">Projects</span>
            </a>
            <?php laink('tickets.tickets')?>
            <span class="icon"><i class="fa-solid fa-ticket"></i></span>
            <span class="text">Tickets</span>
            </a>
            <?php laink('profile')?>
            <span class="icon"><i class="fa-solid fa-user"></i></span>
            <span class="text">Profile</span>
            </a>
        </div>

        <a href="{{ route('login') }}">
            <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
            <span class="text">Logout</span>
        </a>
    </div>
</nav>
