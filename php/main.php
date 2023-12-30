<?php
session_start();
$root = $_SERVER["DOCUMENT_ROOT"];

if (isset($_SESSION["isLoggedIn"])) {
    $isLoggedIn = $_SESSION["isLoggedIn"];
} else {
    $isLoggedIn = false;
}





























































/*
session_start();
date_default_timezone_set(date_default_timezone_get());

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
$all_file = $_SERVER["DOCUMENT_ROOT"];
$all_file .= "/php/all_file_anchor.php";
require($all_file);


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
    $isLoggedIn = false;
}

// Do database health checks

$dbHealth = $root."/php/lib/database_health/table_checks.php";
$dbScheduler = $root."/php/lib/database_health/schedules.php";
require($dbHealth);
require($dbScheduler);
*/