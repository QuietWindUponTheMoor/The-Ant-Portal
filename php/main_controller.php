<?php
$root = $_SERVER["DOCUMENT_ROOT"];
session_start();
date_default_timezone_set(date_default_timezone_get());

$classes = $_SERVER["DOCUMENT_ROOT"];
$classes .= "/php/lib/classes.php";
require($classes);

// If session needs to be destroyed
if (isset($_GET["destroySession"])) {
    $_SESSION["userID"] = null;
    destroy_session();
}
// If setSession is active
if (isset($_GET["setSession"])) {
    $data = array_filter($_GET);
    $setUserID = $data["setSession"];
    $_SESSION["userID"] = $setUserID;
    header("Location: /");
}


// System Checks
if (gethostname() === "DESKTOP-HVA05O2" || gethostname() === "3080RBMS39") {
    // If it's Caleb and Caleb ONLY connecting.
    $SYSTEM_CHECKS = new SystemChecks("main", "image", "main");
    $dbHost = "localhost";
} else {
    // Mostly for devs, as Caleb needs to connect to the "localhost" domain whereas all other devs need to connect to the external redirect for the database host.
    $dbHost = "127.0.0.1:3306";
    $SYSTEM_CHECKS = new SystemChecks("main", "image", "main", $dbHost);
}


$isLoggedIn = $SYSTEM_CHECKS->isLoggedIn;
$isAdmin = $SYSTEM_CHECKS->isAdmin;
$isMod = $SYSTEM_CHECKS->isModerator;
// Assign database
$db = $SYSTEM_CHECKS->db;

// User:
if ($isLoggedIn === true) {
    $username = $SYSTEM_CHECKS->username;
    $userID = $SYSTEM_CHECKS->userID;
    $userPFP = $SYSTEM_CHECKS->userPFP;
} else {
    // 0 userID is automatically invalid but assigns the variable a value at least
    $userID = 0;
}

// Do database health checks

$dbHealth = $root."/php/lib/database_health/table_checks.php";
$dbScheduler = $root."/php/lib/database_health/schedules.php";
require($dbHealth);
require($dbScheduler);
