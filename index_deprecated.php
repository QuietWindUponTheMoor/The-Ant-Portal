<?php
$head = $_SERVER["DOCUMENT_ROOT"];
$head .= "/includes/head.php";
$nav = $_SERVER["DOCUMENT_ROOT"];
$nav .= "/includes/navbar.php";
$footer = $_SERVER["DOCUMENT_ROOT"];
$footer .= "/includes/footer.php";
require($head);
require($nav);
?>

<body>
    <div class="container main-feed-container">


        

        <div class="create-post-button-container">
            <p class="title">Recent Activity</p>
            <a class="btn-main" id="create-post-button" href="/create_post/">Create Post</a>
        </div>

        <div class="posts-container">
            <?php
            $res = $db->selectAll("SELECT * FROM posts ORDER BY postID DESC LIMIT 20;");
            if ($res->num_rows > 0) {
                while ($d = mysqli_fetch_assoc($res)) {
                    $postID = $d["postID"];
                    $fromUserID = $d["userID"];
                    $type = $d["type"];
                    $title = $d["title"];
                    $body = $d["text"];
                    $views = $d["views"];
                    $upvotes = $d["upvotes"];
                    $downvotes = $d["downvotes"];
                    $datetime = $d["datetime"];
                    $tagOne = $d["tagOne"];
                    $tagTwo = $d["tagTwo"];
                    $tagThree = $d["tagThree"];
                    $tagFour = $d["tagFour"];
                    $tagFive = $d["tagFive"];
            
                    if ($type === 1) {
                        $type = "Question";
                    } else if ($type === 2) {
                        $type = "Sighting";
                    } else if ($type === 3) {
                        $type = "Nuptial Flight";
                    } else {
                        $type = $type;
                    }
                    $userdata = getUserData($fromUserID, $db);
                    $username = $userdata[0];
                    $userImage = $userdata[1];

                    // Temporary
                    $editedUserID = "-1";
                    $editedUsername = "?";
                    $editedProfileImage = "/web_images/defaults/default_pfp.jpg";
                    $editedTime = "{date} @ {time} (Timezone)";
            
                    echo
                    '
                    <div class="post">
                        <div class="content">
                            <a class="post-title ellipsis" href="/posts?postID='.$postID.'">'.$title.'</a>
                            <p class="text-preview twoline-ellipsis">'.$body.'</p>
                        </div>
                        <div class="meta">
                            <p class="meta-info" id="post-type">'.$type.'</p>
                        </div>
                        <div class="meta">
                            <p class="meta-info" id="upvotes">'.$upvotes.' upvotes</p>
                            <p class="meta-info" id="downvotes">'.$downvotes.' downvotes</p>
                            <p class="meta-info" id="answers">? answers</p>
                            <p class="meta-info" id="views">'.$views.' views</p>
                        </div>
                        <div class="meta" id="posted-by-user">
                            <div class="user">
                                <div class="user-image-container"><img class="user-image" src="'.$userImage.'"/></div>
                                <a class="username" href="/users/user?userID='.$fromUserID.'">'.$username.'</a>
                                <p class="time">posted on '.$datetime.'</p>
                            </div>
                        </div>
                        <div class="meta"id="edited-by-user">
                            <div class="user">
                                <p class="time">edited on '.$editedTime.' by</p>
                                <div class="user-image-container"><img class="user-image" src="'.$editedProfileImage.'"/></div>
                                <a class="username" href="/users/user?userID='.$editedUserID.'">'.$editedUsername.'</a>
                            </div>
                        </div>
                    </div>
                    ';
                }
            }

            function getUserData($userID, $db) {
                $res = $db->select("SELECT * FROM users WHERE userID=?;", "i", $userID);
                if ($res->num_rows > 0) {
                    $d = mysqli_fetch_assoc($res);
                    $username = $d["username"];
                    $image = $d["image"];
                    return [$username, $image];
                }
            }
            ?>
        </div>

    </div>
</body>

<?php require($footer); ?>