<?php
// Change ini settings
ini_set("session.cookie_lifetime", $lifetime);
ini_set("session.gc_maxlifetime", $lifetime);
session_set_cookie_params($lifetime);


if (
    isset($_GET["set_session"]) &&
    isset($_GET["user_id"]) &&
    isset($_GET["username"]) &&
    isset($_GET["email"]) &&
    isset($_GET["profile_image"])
    ) {
    // Set as vars
    $id = $_GET["user_id"];
    $username = $_GET["username"];
    $email = $_GET["email"];
    $image = $_GET["profile_image"];
    // If all else is good, redirect the user to the main page.
    header("Location: /users/0/signin?set_session=true&user_id=$id&username=$username&email=$email&profile_image=$image}");
}