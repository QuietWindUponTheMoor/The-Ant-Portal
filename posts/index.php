<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");

if (!isset($_GET["postID"]) || $_GET["postID"] < 1) {
    header("Location: /");
} else {
    // Set post ID
    $postID = $_GET["postID"];
    $res = $db->select("SELECT * FROM posts WHERE postID=?;", "i", $postID);
    if ($res->num_rows > 0) {
        $r = mysqli_fetch_assoc($res);
        $postID = $r["postID"];
        $posterID = $r["userID"];
        $body = $r["text"];
        $title = $r["title"];
        $posterUsername = fetchUserData($posterID, $db)[0];
        $postersImage = fetchUserData($posterID, $db)[1];
        $editedByUserID = $r["editedByUserID"];
        $editedByUsername = fetchUserData($editedByUserID, $db)[0];
        $editedByUserImage = fetchUserData($editedByUserID, $db)[1];
        $lastEditDatetime = $r["editDatetime"];
        $datePosted = $r["datetime"];
        $views = $r["views"];
        $upvotes = $r["upvotes"];
        $downvotes = $r["downvotes"];
        $imagesProtoArray = $r["imageArray"];
        $imagesArray = explode(", ", $imagesProtoArray);
        $answers = $r["answers"];
        $replies = $r["replies"];
        $tag1 = $r["tagOne"];
        $tag2 = $r["tagTwo"];
        $tag3 = $r["tagThree"];
        $tag4 = $r["tagFour"];
        $tag5 = $r["tagFive"];
        $tagsArray = [$tag1, $tag2, $tag3, $tag4, $tag5];
        $totalVotes = $upvotes - $downvotes;
        // Vote count colors:
        if ($totalVotes < 0) {
            $color = "error";
        } else if ($totalVotes > 0 && $totalVotes < 5) {
            $color = "warning";
        } else if ($totalVotes >= 5) {
            $color = "success";
        } else {
            $color = "";
        }
        $voteButtonOpacities = voteButtonControls($db, $postID, $userID);
        $upvoteButtonOpacity = $voteButtonOpacities[0];
        $downvoteButtonOpacity = $voteButtonOpacities[1];
    } else {
        header("Location: /");
    }
}

function fetchUserData($userID, $db) {
    $res = $db->select("SELECT username, `image` FROM users WHERE userID=?;", "i", $userID);
    if ($res->num_rows > 0) {
        $r = mysqli_fetch_assoc($res);
        $username = $r["username"];
        $image = $r["image"];
        return [$username, $image];
    } else {
        return ["error", "error"];
    }
}

function voteButtonControls($db, $postID, $thisUserID) {
    $res = $db->select("SELECT * FROM question_has_voted WHERE forPostID=? AND userID=?", "ii", $postID, $thisUserID);
    if ($res->num_rows > 0) {
        // User HAS voted for this post/etc before
        $updown = mysqli_fetch_assoc($res)["updown"];
        if ($updown === 1) {
            // The vote was an upvote
            return ["opacity: 1;", "opacity: 0.6;"];
        } else if ($updown === 0) {
            // The vote was a downvote
            return ["opacity: 0.6;", "opacity: 1;"];
        }
    } else {
        // User hasn't voted for this post/etc yet
        return ["opacity: 0.6;", "opacity: 0.6;"];
    }
}

// Get flagging modal text
$flagging_modal = file_get_contents($root."/mini-includes/modals/flag_modal.php");

// Fetch THIS user's username:
$thisUserData = fetchUserData($userID, $db);
$thisUsersUsername = $thisUserData[0];

// Initialize reply type
$replyType = "post";
?>

<body>
    <div class="container">
        <div class="container_sub">
            <?php require($root."/includes/container_left.php"); ?>
            <div class="container_main">


                <div class="post-page-container">
                    <div class="post-section" id="title-container">
                        <p class="post-title" id="title"><?php echo $title; ?></p>
                    </div>
                    <div class="post-section post-flagging">
                        <?php echo $flagging_modal; ?>
                    </div>
                    <div class="post-section vote-container">
                        <div class="vote-subcontainer">
                            <div class="icon-container" title="Upvote this post"><img class="icon vote-image" id="upvote-trigger" style="<?php echo $upvoteButtonOpacity; ?>" src="/web_images/icons/upvote.png"/></div>
                            <p class="vote-count <?php echo $color; ?>" id="total-votes"><?php echo $totalVotes; ?></p>
                            <div class="icon-container" title="Downvote this post"><img class="icon vote-image" id="downvote-trigger" style="<?php echo $downvoteButtonOpacity; ?>" src="/web_images/icons/downvote.png"/></div>
                        </div>
                    </div>
                    <div class="post-section body-container">
                        <p class="body" id="body"><?php echo $body; ?></p>
                    </div>
                    <div class="post-section section-wrap" id="user-data-section">
                        <div class="user-data">
                            <p class="by">Posted by</p>
                            <div class="user-image-container"><img class="user-image" src="<?php echo $postersImage; ?>"/></div>
                            <a class="by by-link" href="/users/user?userID=<?php echo $posterID; ?>"><?php echo $posterUsername; ?></a>
                            <p class="by">on <?php echo $datePosted; ?></p>
                        </div>
                        <?php
                        if ($editedByUserID > 0) {
                            echo
                            '
                            <div class="user-data" id="post-by">
                                <p class="by">Edited by</p>
                                <div class="user-image-container"><img class="user-image" src="'.$editedByUserImage.'"/></div>
                                <a class="by by-link" href="/users/user?userID=1">'.$editedByUsername.'</a>
                                <p class="by">on '.$lastEditDatetime.'</p>
                            </div>
                            ';
                        } else if ($editedByUserID === -1) {
                            echo
                            '
                            <div class="user-data" id="edited-by">
                                <p class="by">Pending edits</p>
                            </div>
                            ';
                        } else {
                            echo
                            '
                            <div class="user-data" id="edited-by">
                                <p class="by">Never edited</p>
                            </div>
                            ';
                        }
                        ?>
                    </div>
                    <div class="post-section section-wrap images-section">
                        <?php
                        foreach ($imagesArray as $image) {
                            echo '<a class="image-container" href="'.$image.'" target="_blank"><img class="post-image" src="'.$image.'"/></a>';
                        }
                        ?>
                    </div>
                    <div class="post-section section-wrap post-meta" id="tags-section">
                        <?php
                        foreach ($tagsArray as $tag) {
                            if ($tag == "NULL" || $tag == "") {
                                continue;
                            }
                            echo '<a class="tag" href="/tags?name='.$tag.'">'.$tag.'</a>';
                        }
                        ?>
                    </div>
                    <div class="post-section section-wrap post-meta">
                        <p class="meta"><?php echo $views; ?> views</p>
                        <p class="meta"><?php echo $upvotes; ?> upvotes</p>
                        <p class="meta"><?php echo $downvotes; ?> downvotes</p>
                        <p class="meta">Posted on <?php echo $datePosted; ?></p>
                    </div>
                    <div class="post-section section-wrap post-meta">
                        <p class="meta"><?php echo $answers; ?> answers</p>
                        <p class="meta"><?php echo $replies; ?> replies</p>
                    </div>
                    <div class="post-section" id="control-buttons">
                        <div class="control-button-container" id="start-editing-container"><img class="control-button" id="start-editing" src="/web_images/icons/editing.png" title="Suggest an edit to this post"/></div>
                        <button style="display: none;" class="btn-secondary" id="cancel-edits" type="button">Cancel Edits</button>
                        <button style="display: none;" class="btn-main" id="finish-edits" type="button">Finish Edits</button>
                    </div>

                    <div class="post-page-comments comments-box-main" id="main-comments-container">
                        <div class="comments-list">
                            <?php
                            $repliesRes = $db->select("SELECT * FROM replies WHERE for_type=? AND forItemID=?;", "si", "post", $postID);
                            if ($repliesRes->num_rows > 0) {
                                while ($row = mysqli_fetch_assoc($repliesRes)) {
                                    $byUserID = $row["byUserID"];
                                    $byUsername = fetchUserData($byUserID, $db);
                                    $byUsername = $byUsername[0];
                                    $text = $row["text"];
                                    $datetime = $row["datetime"];


                                    echo
                                    '
                                    <div class="comment-container">
                                        <div class="comment-options post-flagging">
                                            '.$flagging_modal.'
                                        </div>
                                        <div class="comment-content">
                                            <a class="user-link" href="/users/user?userID='.$byUserID.'">'.$byUsername.'</a>
                                            <p class="comment">'.$text.'</p>
                                            <p class="comment comment_date">'.$datetime.'</p>
                                        </div>
                                    </div>
                                    ';
                                }
                            }
                            ?>
                        </div>
                        <div class="comment-box-write">
                            <div class="textarea-container textarea-container-inactive">
                                <textarea class="comment-textbox" id="comment-textbox" name="comment" minlength="1" maxlength="256" rows="1" placeholder="Add Comment"></textarea>
                                <button class="btn-action post-comment-button" type="button" onclick='submitReply("<?php echo $replyType; ?>", $(this));'>Reply</button>
                            </div>
                        </div>
                    </div>

                    <div class="page-divider"><?php $replyType = "answer"; ?></div>

                </div>


            </div>
            <?php require($root."/includes/container_right.php"); ?>
        </div>
    </div>
</body>
<script type="text/javascript">
// Get backend data
// User
const $isLoggedIn = <?php if ($isLoggedIn === false) {echo false;} else {echo true;} ?>;
const $flaggingModal = `<?php echo $flagging_modal; ?>`;
const user_data = {
    id: <?php echo $userID; ?>,
    username: "<?php echo $thisUsersUsername; ?>",
};
// Post
const post_data = {
    db: "<?php echo $dbHost; ?>",
    postType: 1, // 1 = post
    postID: <?php echo $postID; ?>,
};
</script>
<script type="text/javascript" src="/js/lib/page_control/posts.js"></script>

<?php require($root."/includes/footer.php"); ?>