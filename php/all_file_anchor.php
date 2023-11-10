<?php
$classes = $_SERVER["DOCUMENT_ROOT"];
$classes .= "/php/lib/classes.php";
require($classes);

// Website link:
$websiteLink = "http://173.25.108.108";


// Caleb's version of the hostname
$cHost = "localhost";
// Everyone else's version of the hostname
$eHost = "127.0.0.1:3306";

if (gethostname() === "DESKTOP-HVA05O2" || gethostname() === "3080RBMS39") {
    // If it's Caleb and Caleb ONLY connecting.
    $SYSTEM_CHECKS = new SystemChecks("main", "image", "main", $cHost);
    $dbHost = $cHost;
} else {
    // Mostly for devs, as Caleb needs to connect to the "localhost" domain whereas all other devs need to connect to the external redirect for the database host.
    $dbHost = $eHost;
    $SYSTEM_CHECKS = new SystemChecks("main", "image", "main", $eHost);
};