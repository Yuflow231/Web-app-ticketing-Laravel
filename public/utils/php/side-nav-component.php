<?php
    require_once("debug-handler.php");

    $user = $_SESSION['user'];


    // Initialize debug handler
    $debugHandler = DebugHandler::getInstance();

    $currentFile = basename($_SERVER['PHP_SELF']);
    $pageName = pathinfo($currentFile, PATHINFO_FILENAME);
    $pagePrefix = substr($pageName, 0, 5);

    // Add additional debug info

    $debugHandler->addInfoLeft("Current page",  $pageName);
    $debugHandler->addInfoLeft("Page's prefix",  $pagePrefix);

    // Get debug parameter for URLs
    $debug = $debugHandler->getDebugParam();

    /**
     * Helper function to generate navigation links with proper active state and debug param
     * @param string $refLink The target URL
     */
    function laink(string $refLink): void {
        global $pagePrefix, $debug;

        $linkBasename = basename($refLink);
        $isActive = str_contains($linkBasename, $pagePrefix);
        $activeClass = $isActive ? ' class="active"' : '';

        echo "<a href='{$refLink}{$debug}'{$activeClass}>";
    }
?>

<nav class="navigation">
    <header class="top-bar">
        <div class="menu-bar">
            <span class="hamburger"><i class="fa-solid fa-bars"></i></span>
            <span>| Ticketing App</span>
        </div>
        <div class="user-profile-header">
            <a href="/Web-app-ticketing-php/src/pages/profile.php<?= $debug ?>" class="user-profile-inline">
                <span class="username" data-type="first-name"><?= $user["first_name"] ?></span>
                <span class="username" data-type="last-name"><?= $user["last_name"] ?></span>

                <img src="/Web-app-ticketing-php/src/assets/images/<?= $user["profile_pic"] ?>" alt="User Profile" class="profile-pic" >
            </a>
        </div>
    </header>

    <!-- Side Navigation Bar -->
    <div class="side-nav">
        <div class="top-side">
            <?php  laink("/Web-app-ticketing-php/src/pages/dashBoard.php") ?>
                <span class="icon"><i class="fa-solid fa-chart-line"></i></span>
                <span class="text">Dashboard</span>
            </a>
            <?php  laink("/Web-app-ticketing-php/src/pages/projects/projects.blade.php") ?>
                <span class="icon"><i class="fa-solid fa-diagram-project"></i></span>
                <span class="text">Projects</span>
            </a>
            <?php  laink("/Web-app-ticketing-php/src/pages/tickets/tickets.blade.php") ?>
                <span class="icon"><i class="fa-solid fa-ticket"></i></span>
                <span class="text">Tickets</span>
            </a>
            <?php  laink("/Web-app-ticketing-php/src/pages/profile.blade.php") ?>
                <span class="icon"><i class="fa-solid fa-user"></i></span>
                <span class="text">Profile</span>
            </a>
        </div>

        <a href="/Web-app-ticketing-php/src/assets/php/logout.php">
            <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
            <span class="text">Logout</span>
        </a>
    </div>
</nav>
