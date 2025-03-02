<?php
session_start();

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Delete the cookies
if (isset($_COOKIE['loggedin_username'])) {
    // Expire the cookie by setting time in the past
    setcookie("loggedin_username", "", time() - 3600, "/");
}

// Redirect to the login page
header("Location: index.php");
exit();
