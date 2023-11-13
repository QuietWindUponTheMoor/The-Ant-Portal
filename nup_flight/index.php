<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");

if (!isset($_GET["flightID"]) || $_GET["flightID"] < 1) {
    header("Location: /");
} else {
    // Set flight ID
    $flightID = $_GET["flightID"];
    $res = $db->select("SELECT * FROM nuptial_flights WHERE flightID=?;", "i", $flightID);
    if ($res->num_rows > 0) {
        $r = mysqli_fetch_assoc($res);
        $flightID = $r["flightID"];
        $posterID = $r["userID"];
        $species = $r["species"];
        $body = $r["text"];
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
        $tempF = $r["temperature"];
        $tempC = convertToFToC($tempF);
        $moonCycle = $r["moon_cycle"];
        $windSpeed = $r["wind_speed"];
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
            $color = "success";
        } else if ($totalVotes > 0 && $totalVotes < 5) {
            $color = "warning";
        } else if ($totalVotes >= 5) {
            $color = "success";
        } else {
            $color = "";
        }
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
function convertToFToC($inputString) {
    // Extract numeric value and temperature unit
    preg_match('/([-+]?\d*\.?\d+)\s*°F/', $inputString, $matches);

    if (count($matches) >= 2) {
        $numericValue = $matches[1];

        // Convert to Celsius
        $celsiusValue = ($numericValue - 32) * (5/9);

        // Return the result with the °C unit
        return round($celsiusValue, 2) . '°C';
    }

    // Return the original string if the format is not recognized
    return $inputString;
}
?>

<body>
    <div class="container">
        <div class="container_sub">
            <?php require($root."/includes/container_left.php"); ?>
            <div class="container_main">


                <div class="post-page-container">
                    <div class="post-section">
                        <p class="post-title">Nuptial Flight #<?php echo $flightID; ?>: <?php echo $species; ?></p>
                    </div>
                    <div class="post-section vote-container">
                        <div class="vote-subcontainer">
                            <div class="icon-container"><img class="icon" id="upvote-trigger" src="/web_images/icons/upvote.png"/></div>
                            <p class="vote-count <?php echo $color; ?>" id="total-votes"><?php echo $totalVotes; ?></p>
                            <div class="icon-container"><img class="icon" id="downvote-trigger" src="/web_images/icons/downvote.png"/></div>
                        </div>
                    </div>
                    <div class="post-section">
                        <p class="body"><?php echo $body; ?></p>
                    </div>
                    <div class="post-section section-wrap">
                        <div class="user-data">
                            <p class="by">Posted by</p>
                            <div class="user-image-container"><img class="user-image" src="<?php echo $postersImage; ?>"/></div>
                            <a class="by by-link" href="/users/user?userID=<?php echo $posterID; ?>"><?php echo $posterUsername; ?></a>
                            <p class="by">on <?php echo $datePosted; ?></p>
                        </div>
                        <?php
                        if ($editedByUserID != 0) {
                            echo
                            '
                            <div class="user-data" id="edited-by">
                                <p class="by">Edited by</p>
                                <div class="user-image-container"><img class="user-image" src="'.$editedByUserImage.'"/></div>
                                <a class="by by-link" href="/users/user?userID=1">'.$editedByUsername.'</a>
                                <p class="by">on '.$lastEditDatetime.'</p>
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
                    <div class="post-section section-wrap post-meta">
                        <p class="meta">Temperature: <?php echo $tempF; ?> (<?php echo $tempC; ?>)</p>
                        <p class="meta">Wind Speed: <?php echo $windSpeed; ?>mph</p>
                        <p class="meta">Moon Cycle: <?php echo $moonCycle; ?></p>
                    </div>
                    <div class="post-section section-wrap post-meta">
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
                </div>


            </div>
            <?php require($root."/includes/container_right.php"); ?>
        </div>
    </div>
</body>

<?php require($root."/includes/footer.php"); ?>