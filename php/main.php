<?php
session_start();
$root = $_SERVER["DOCUMENT_ROOT"];

if (isset($_SESSION["isLoggedIn"])) {
    $isLoggedIn = $_SESSION["isLoggedIn"];
} else {
    $isLoggedIn = 0;
}

// Get JS imports
$jsImports = [
    "functions" => '<script type="text/javascript" src="/js/lib/system/functions_list.js"></script>',

];




// Get client device, ONLY USE THIS FOR DEVELOPMENT
$device = $_SERVER["REMOTE_ADDR"];
if ($device === "::1") {// If client is lead dev's device, use localhost address for node server
    $API_host = "http://127.0.0.1:81/";
} else { // If client is a team member's device
    $API_host = "http://173.25.108.108:81/";
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