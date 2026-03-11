<?php
session_start();
session_destroy();
header("Location: /Web-app-ticketing-php/login.blade.php?toast=logged_out");
exit;
