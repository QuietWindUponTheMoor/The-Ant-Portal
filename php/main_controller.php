<?php
session_start();
date_default_timezone_set(date_default_timezone_get());

$classes = $_SERVER["DOCUMENT_ROOT"];
$classes .= "/php/lib/classes.php";
require($classes);

function dateCalc($i) {
    date_default_timezone_set(date_default_timezone_get());
    $c = time();
    $dif = $c - $i;
    if ($dif < (60)) {
        if ($dif < 1) {return $dif.' second ago';}
        else {return $dif.' seconds ago';}
    }
    if ($dif > (60) && $dif < (3600)) {
        $r = floor($dif/60);
        if ($r < 1) {return $r.' minute ago';}
        else {return $r.' minutes ago';}
    }
    if ($dif > (3600) && $dif < (86400)) {
        $r = floor($dif/(3600));
        if ($r < 2) {return $r.' hour ago';}
        else {return $r.' hours ago';}
    }
    if ($dif > (86400) && $dif < (604800)) {
        $r = floor($dif/(86400));
        if ($r < 2) {return $r.' day ago';}
        else {return $r.' days ago';}
    }
    if ($dif > (604800) && $dif < (2419200)) {
        $r = floor($dif/(604800));
        if ($r < 2) {return $r.' week ago';}
        else {return $r.' weeks ago';}
    }
    if ($dif > (2419200) && $dif < (29030400)) {
        $r = floor($dif/(2419200));
        if ($r < 2) {return $r.' month ago';}
        else {return $r.' months ago';}
    }
    if ($dif > (29030400)) {
        $r = floor($dif/(29030400));
        if ($r < 2) {return $r.' year ago';}
        else {return $r.' years ago';}
    }
}



// If setSession is active
if (isset($_GET["setSession"])) {
    $data = array_filter($_GET);
    $setUserID = $data["setSession"];
    $_SESSION["userID"] = $setUserID;
    header("Location: /");
}


// System Checks
if (gethostname() === "DESKTOP-HVA05O2") {
    // If it's Caleb and Caleb ONLY connecting.
    $SYSTEM_CHECKS = new SystemChecks("main", "image", "main");
} else {
    // Mostly for devs, as Caleb needs to connect to the "localhost" domain whereas all other devs need to connect to the external redirect for the database host.
    $SYSTEM_CHECKS = new SystemChecks("main", "image", "main", "173.25.108.108:3306");
}


$isLoggedIn = $SYSTEM_CHECKS->isLoggedIn;
$isAdmin = $SYSTEM_CHECKS->isAdmin;
// Assign database
$db = $SYSTEM_CHECKS->db;

// User:
if ($isLoggedIn === true) {
    $username = $SYSTEM_CHECKS->username;
    $userID = $SYSTEM_CHECKS->userID;
    $userPFP = $SYSTEM_CHECKS->userPFP;
}