<?php

$classes = $_SERVER["DOCUMENT_ROOT"];
$classes .= "/php/lib/classes.php";
require($classes);

$d = array_filter($_POST);
$db = $d["db"];
$userID = $d["userID"];
$reason = $d["flag-reason"];
$explanation = $d["explanation"];
$itemLink = "/test/test/test/";

$flagging = new Flagging($db, $userID);

// Submit report
echo $flagging->flagSubmitted($reason, $itemLink, $explanation);
