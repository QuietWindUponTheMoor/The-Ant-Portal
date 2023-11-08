<?php
$head = $_SERVER["DOCUMENT_ROOT"];
$head .= "/includes/head.php";
$nav = $_SERVER["DOCUMENT_ROOT"];
$nav .= "/includes/navbar.php";
$footer = $_SERVER["DOCUMENT_ROOT"];
$footer .= "/includes/footer.php";
require($head);
require($nav);

if (!isset($_GET["userID"]) || $_GET["userID"] < 1) {
    header("Location: /");
}

if ($isLoggedIn === true && isset($_GET["userID"]) && $_GET["userID"] == $userID) {
    // If the current user is the owner of this profile
    $isOwner = true;
} else {
    // If user is not the owner of this profile
    $isOwner = false;
}

// Require the get_user_data.php file
$forms = $_SERVER["DOCUMENT_ROOT"];
$forms .= "/php/lib/profile/get_user_data.php";
require($forms);
?>

<body>
    <div class="profile-container">
        <div class="profile-top" id="banner-container">
            <div class="profile-top-content">
                <?php
                if ($isOwner === true) {
                    echo '<div class="profile-image"><img class="main-image main-image-on-hover" src="'.$profileImage.'"/></div>';
                } else {
                    echo '<div class="profile-image"><img class="main-image" src="'.$profileImage.'"/></div>';
                }
                ?>
                <p class="profile-name"><?php echo $thisUsername; ?></p>
                <div class="profile-meta">
                    <p class="meta" id="creation-date">Profile created <?php echo "$date $time"; ?></p>
                    <p class="meta" id="rank"><?php echo $rank; ?></p>
                    <p class="meta" id="seeds"><?php echo $seeds; ?> seeds</p>
                </div>
            </div>
            <!-- Change username form modal -->
            <div class="profile-modal" id="change-username-modal">
                <div class="modal-top">
                    <p class="title">Change your username</p>
                    <p class="cancel" id="cancel-username" onclick="$('#change-username-modal').css('display', 'none');">X</p>
                </div>
                <form id="change-username-form" action="" method="POST">
                    <input type="text" id="username" name="username" minlength="3" maxlength="24" required/>
                    <input type="hidden" name="type" value="change-username"/>
                    <button class="btn-main" id="submit-username" type="submit">Change Username</submit>
                </form>
            </div>

        </div>
        <div class="profile-bottom">
            <!-- This will contain everything else in the profile -->
            <?php
            if ($isOwner === true) {
                echo
                '
                <div class="profile-settings">
                    <div class="settings-sub">
                        <a class="setting" id="change-username" onclick="$(\'#change-username-modal\').css(\'display\', \'flex\');">Change Username</a>
                        <a class="setting" id="change-banner" onclick="$(\'#banner\').click();">Change Banner</a>
                        <a class="setting unfinished" id="" href="#">Setting 3</a>
                        <a class="setting unfinished" id="" href="#">Setting 4</a>
                        <a class="setting unfinished" id="delete-account" href="#">Delete Account</a>
                        <a class="setting" id="profile-signout" href="/users/logout/">Sign Out</a>
                    </div>
                </div>
                ';
            }
            ?>
            <div class="profile-feed">
                <div class="feed-sub">
                    <?php
                    $res = $db->select("SELECT * FROM posts WHERE userID=? ORDER BY postID DESC;", "i", $_GET["userID"]);
                    if ($res->num_rows > 0) {
                        while ($d = mysqli_fetch_assoc($res)) {
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
                            $userdata = getUserData($_GET["userID"], $db);
                            $username = $userdata[0];
                            $userImage = $userdata[1];
                    
                            echo
                            '
                            <div class="post">
                                <div class="content">
                                    <a class="post-title ellipsis" href="#">'.$title.'</a>
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

                                <div class="meta">
                                    <div class="user">
                                        <div class="user-image-container"><img class="user-image" src="'.$userImage.'"/></div>
                                        <a class="username" href="/users/user?userID='.$fromUserID.'">'.$username.'</a>
                                        <p class="time">posted on '.$datetime.'</p>
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
        </div>

        <!-- Forms & controls -->
        <?php
        $forms = $_SERVER["DOCUMENT_ROOT"];
        $forms .= "/php/lib/profile/forms.php";
        require($forms);
        ?>
        <form id="change-profile-image-form" style="display: none;" action="" method="POST" enctype="multipart/form-data">
            <input type="file" id="image" name="image" accept="images/*" required/>
            <input type="hidden" name="type" value="profile-image"/>
        </form>
        <form id="change-profile-banner-form" style="display: none;" action="" method="POST" enctype="multipart/form-data">
            <input type="file" id="banner" name="banner" accept="images/*" required/>
            <input type="hidden" name="type" value="banner-image"/>
        </form>


    </div>
</body>
<script type="text/javascript" src="/js/lib/profile/forms.js"></script>
<script type="text/javascript">
// Handle rank colors & only handle if NOT a regular user
let rankBefore = <?php echo $rankBefore; ?>;
if (rankBefore === 1) {
    // User is moderator
    $("#rank").css("color", "white").css("background-color", "blue");
} else if (rankBefore === 2) {
    // User is administrator
    $("#rank").css("color", "white").css("background-color", "gold");
}

$("#banner-container").css("background-image", "url('<?php echo $banner; ?>')");
</script>

<?php require($footer); ?>