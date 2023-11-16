<?php
// Require class file
require("vote_class.php");

$data = array_filter($_POST);
$voteType = $data["voteType"];
$postID = $data["postID"];
$userID = $data["userID"];
$upvoteOrDownvote = $data["upvoteOrDownvote"]; // 1 = upvote, 2 = downvote

$votes = new Voting($db, $voteType, $postID, $userID, $upvoteOrDownvote);