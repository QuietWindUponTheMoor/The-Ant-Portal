<?php
// Start the session
session_start();

if (isset($_GET["set"])) {
    // Set the session but first set session timeout
    $lifetime = 60 * 60 * 12; // 12 hours
    ini_set("session.cookie_lifetime", $lifetime);
    ini_set("session.gc_maxlifetime", $lifetime);
    session_set_cookie_params($lifetime);
    $_SESSION["isLoggedIn"] = true;
    header("Location: /");
}