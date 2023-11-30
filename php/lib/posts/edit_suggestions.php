<?php

$classes = $_SERVER["DOCUMENT_ROOT"];
$classes .= "/php/lib/classes.php";
require($classes);

$data = array_filter($_POST);

$db = new Database($data["db"], "root", "", "main");
$postType = $data["postType"];
$postID = $data["postID"];
$userID = $data["userID"];
$title = $data["newTitle"];
$body = $data["newBody"];

$db->insert("INSERT INTO post_suggestions (suggestedByUserID, postType, postID, newtitle, newBody) VALUES (?, ?, ?, ?, ?);", "iiiss", $userID, $postType, $postID, $title, $body);

if ($postType === "4") {
    // Post is nup flight
    if ($db->insert("UPDATE nuptial_flights SET editedByUserID=? WHERE flightID=?;", "ii", -1, $postID)) {
        echo 1;
    } else {
        echo 0;
    }
} else {
    // Post is anything else (so far, subject to change)
    if ($db->insert("UPDATE posts SET editedByUserID=? WHERE postID=?;", "ii", -1, $postID)) {
        echo 1;
    } else {
        echo 0;
    }
}