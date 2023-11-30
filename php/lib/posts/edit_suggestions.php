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

echo 1;