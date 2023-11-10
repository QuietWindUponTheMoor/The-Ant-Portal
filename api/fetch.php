<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/php/all_file_anchor.php");
require("generate_response.php");

// Initialize Responses() class
$responses = new Responses($websiteLink);

// Check for request data
$req = array_filter($_GET) ?? array_filter($_POST);

// Test
//print_r($req);

// Initialize response
$res = array();

// Check request type:
$reqType = $req["type"];
if ($reqType === "post") {
    $postType = $req["post_type"] ?? 0; // 0 = all posts/no preference
    $maxLength = $req["max_length"] ?? 10; // Default max of 10
    $order = $req["order"] ?? 1; // Default ASC

    // Validate info
    if ($postType == "1" || $postType == "2" || $postType == "3") {
        array_push($res, $responses->sendPostData(1, "test", "body goes here", "link_one, link_two, link_three", 112, 5, ["tag1", "tag2", "tag3"], 5));
    } else {
        // If post type is not valid
        array_push($res, $responses->invalidReq("post_type=".$postType));
    }
    
    if ($maxLength < 1 || $maxLength > 100) {
        // If max length is incorrect/invalid
        array_push($res, $responses->invalidReq("max_length=".$postType));
    }
}




// Finally, give response
echo "\"response\": [<br>";
echo implode(", ", $res);
echo "<br>]";