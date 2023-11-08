<?php

// Get profile data
$res = $db->select("SELECT * FROM users WHERE userID=?;", "i", $_GET["userID"]);
if ($res->num_rows > 0) {
    $data = mysqli_fetch_assoc($res);
    $profileImage = $data["image"];
    $thisUsername = $data["username"];
    $rankBefore = $data["rank"];
    $seeds = $data["seeds"];
    $postsCount = $data["posts"];
    $repliesCount = $data["replies"];
    $questionsCount = $data["questions"];
    $answersCount = $data["username"];
    $date = $data["joined"];
    $time = $data["time"];
    $banner = $data["banner"];
} else {
    die("There was an error collecting this user's profile image. Please try again or contact an administrator.");
}

// Handle ranks
if ($rankBefore === 0) {
    // User is regular user
    $rank = "User";
} else if ($rankBefore === 1) {
    // User is moderator
    $rank = "Moderator";
} else if ($rankBefore === 2) {
    // User is administrator
    $rank = "Administrator";
}