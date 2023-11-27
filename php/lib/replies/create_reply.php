<?php

$classes = $_SERVER["DOCUMENT_ROOT"];
$classes .= "/php/lib/classes.php";
require($classes);

$data = array_filter($_POST);

$db = $data["db"];
$db = new Database($db, "root", "", "main");
$userID = $data["userID"];
$forType = $data["forType"];
$itemID = $data["itemID"];
$text = $data["text"];
$datetime = $data["datetime"];



$db->insert("INSERT INTO replies (byUserID, for_type, forItemID, `text`, `datetime`) VALUES (?, ?, ?, ?, ?);", "isiss", $userID, $forType, $itemID, $text, $datetime);

// If all is good
echo 1;