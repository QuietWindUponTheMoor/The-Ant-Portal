<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/php/all_file_anchor.php");

// Check for request data
$req = array_filter($_GET) ?? array_filter($_POST);

print_r($req);

// Check request type:
$reqType = $req["type"];
if ($reqType === "post") {
    $postType = $req["post_type"] ?? 0; // 0 = all posts/no preference
    $maxLength = $req["max_length"] ?? 10; // Default max of 10
    $order = $req["order"] ?? 1; // Default ASC


}