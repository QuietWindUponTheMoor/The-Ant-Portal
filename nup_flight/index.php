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
        $voteButtonOpacities = voteButtonControls($db, $flightID, $userID);
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

function voteButtonControls($db, $postID, $thisUserID) {
    $res = $db->select("SELECT * FROM nf_has_voted WHERE forFlightID=? AND userID=?", "ii", $postID, $thisUserID);
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
                    <div class="post-section post-flagging">
                        <div class="flag-image-container">
                            <img class="flag-image" id="flag-image" src="/web_images/icons/flag.png" title="Flag this post to let us know something is wrong."/>
                            <div class="flagging-modal modal-main">
                                <form class="modal-section flagging-form" id="flagging-form" action="" method="POST">
                                    <div class="modal-sub-section">
                                        <p class="modal-title" id="flagging-response">Please tell us what you're flagging this content for.</p>
                                    </div>
                                    <div class="modal-sub-section radio-section">
                                        <input type="radio" id="irrelevant-content" name="flag-reason" value="This post is irrelevant (spam, off-topic, etc)" required/>
                                        <label for="irrelevant-content">This post is irrelevant (spam, off-topic, etc)</label>
                                    </div>
                                    <div class="modal-sub-section radio-section">
                                        <input type="radio" id="confusing-or-misguided" name="flag-reason" value="This post is too confusing or misguided" required/>
                                        <label for="confusing-or-misguided">This post is too confusing or misguided</label>
                                    </div>
                                    <div class="modal-sub-section radio-section">
                                        <input type="radio" id="illegals" name="flag-reason" value="This post promotes the act of buying, selling, or trading illegal (non-native to the OP's location) ants, without proper permits." required/>
                                        <label for="illegals">This post promotes the act of buying, selling, or trading illegal (non-native to the OP's location) ants, without proper permits.</label>
                                    </div>
                                    <div class="modal-sub-section radio-section">
                                        <input type="radio" id="bullying" name="flag-reason" value="This post promotes the act of bullying (racism, homophobia, etc)" required/>
                                        <label for="bullying">This post promotes the act of bullying (racism, homophobia, etc)</label>
                                    </div>
                                    <div class="modal-sub-section radio-section">
                                        <input type="radio" id="rude-or-abusive" name="flag-reason" value="This post has rude or abusive content." required/>
                                        <label for="rude-or-abusive">This post has rude or abusive content.</label>
                                    </div>
                                    <div class="modal-sub-section radio-section">
                                        <input type="radio" id="duplicate" name="flag-reason" value="This post is a duplicate of another post or is too similar to another post to be relevant." required/>
                                        <label for="duplicate">This post is a duplicate of another post or is too similar to another post to be relevant.</label>
                                    </div>
                                    <div class="modal-sub-section radio-section">
                                        <input type="radio" id="plagiarism" name="flag-reason" value="This post contains plagiarized content from elsewhere." required/>
                                        <label for="plagiarism">This post contains plagiarized content from elsewhere.</label>
                                    </div>
                                    <div class="modal-sub-section radio-section">
                                        <input type="radio" id="something-else" name="flag-reason" value="Something else (Please write the reason below)." required/>
                                        <label for="something-else">Something else (Please write the reason below).</label>
                                    </div>
                                    <div class="modal-sub-section explanation-section">
                                        <label for="explanation">Explain your report or add additional info here. If your post is in regards to plagiarism, please provide a link. If there is a particular user, answer or reply at fault, please mention those here. If this is a duplicate to a post, please provide a link to the post it's a duplicate of.</label>
                                        <textarea class="input-main" type="text" id="explanation" name="explanation" minlength="35" maxlength="30000" placeholder="Please explain more about your report here." required></textarea>
                                    </div>
                                    <div class="modal-sub-section final-section">
                                        <input type="hidden" id="user-id" name="userID" value="<?php echo $userID; ?>" required/>
                                        <input type="hidden" id="db" name="db" value="<?php echo $dbHost; ?>" required/>
                                        <button class="btn-secondary" id="cancel-flagging" type="button">Cancel</button>
                                        <button class="btn-main" id="submit-flagging" type="submit" name="submit">Submit Flag</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="post-section vote-container">
                        <div class="vote-subcontainer">
                            <div class="icon-container" title="Upvote this post"><img class="icon" id="upvote-trigger" style="<?php echo $upvoteButtonOpacity; ?>" src="/web_images/icons/upvote.png"/></div>
                            <p class="vote-count <?php echo $color; ?>" id="total-votes"><?php echo $totalVotes; ?></p>
                            <div class="icon-container" title="Downvote this post"><img class="icon" id="downvote-trigger" style="<?php echo $downvoteButtonOpacity; ?>" src="/web_images/icons/downvote.png"/></div>
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
<script type="text/javascript">
// Subitting flags
const flagging_response = $("#flagging-response");
let $radioContainers = $(".radio-section");
$("#flagging-form").on("submit", async (event) => {
    // Prevent default actions
    event.preventDefault();
    // Get form data
    const formData = new FormData(document.getElementById("flagging-form"));

    // Submit the form
    await sendAJAX("/php/lib/flagging/submit_report.php", formData, "POST", false, false, function (unparsedResponse) {
        const response = JSON.parse(unparsedResponse);
        const resCode = response.code;
        const reason = response.reason;
        const link = response.reported_from;
        const explanation = response.report_explanation;
        const success = response.success;
        const error = response.error;
        if (resCode === 1) {
            // Success
            document.getElementById("flagging-form").reset();
            $radioContainers.hide();
            $("#submit-flagging").hide();
            $(".explanation-section").hide();
            $("#cancel-flagging").text("Finished");
            flagging_response.html(success).addClass("success");
        } else {
            // Error
            flagging_response.html(error).addClass("error");
        }
    });
});


// Initial widths of flagging modal
let sectionWidth = $(".post-flagging").width();
$(".flagging-modal").css("width", Math.floor(sectionWidth)).css("max-width", Math.floor(sectionWidth) + "px");
// Dynamic widths
$(window).on("resize", () => {
    let sectionWidth = $(".post-flagging").width();
    $(".flagging-modal").css("width", Math.floor(sectionWidth)).css("max-width", Math.floor(sectionWidth) + "px");
});

// Selected flagging reasons controls
$radioContainers.find(":radio").on("change", e => {
    $radioContainers.removeClass("radio-active"); // remove from all containers
    $(e.target).closest(".radio-section").addClass("radio-active"); // add class to current
});

// Opening/closing the flagging modal
$("#flag-image").on("click", () => {
    $(".flagging-modal").css("display", "flex").hide().fadeIn();
});
$("#cancel-flagging").on("click", () => {
    $(".flagging-modal").hide();
});

// Change image color to red upon hover of flag button
$("#flag-image").hover((event) => {
    if (event.type === "mouseenter") {
        $("#flag-image").attr("src", "/web_images/icons/flag_red.png");
    } else {
        $("#flag-image").attr("src", "/web_images/icons/flag.png");
    }
});

// Upvote triggers
$("#upvote-trigger").on("click", async () => {
    await upvote();
});
$("#downvote-trigger").on("click", async () => {
    await downvote();
});

// Helpers
async function upvote() {
    $("#upvote-trigger").css("opacity", "0.6");
    $("#downvote-trigger").css("opacity", "0.6");
    $.ajax({  
        type: "POST",  
        url: "/php/lib/voting/post_voting.php", 
        data: {
            voteType: 2,
            postID: <?php echo $flightID; ?>,
            userID: <?php echo $userID; ?>,
            upvoteOrDownvote: 1
        },
        success: function(response) {
            // ParseInt--if parseInt DOESN'T result in "NaN", then it was a success.
            if (response === NaN) {
                // Not successful
                alert(response);
            } else if (response !== NaN) {
                $("#total-votes").text(response);
                $("#upvote-trigger").css("opacity", "1");
            } else {
                alert(response);
            }
        }
    });
}
async function downvote() {
    $("#upvote-trigger").css("opacity", "0.6");
    $("#downvote-trigger").css("opacity", "0.6");
    $.ajax({  
        type: "POST",  
        url: "/php/lib/voting/post_voting.php", 
        data: {
            voteType: 2,
            postID: <?php echo $flightID; ?>,
            userID: <?php echo $userID; ?>,
            upvoteOrDownvote: 2
        },
        success: function(response) {
            // ParseInt--if parseInt DOESN'T result in "NaN", then it was a success.
            if (response === NaN) {
                // Not successful
                alert(response);
            } else if (response !== NaN) {
                $("#total-votes").text(response);
                $("#downvote-trigger").css("opacity", "1");
            } else {
                alert(response);
            }
        }
    });
}
async function sendAJAX(url, dataObj, method, processData, contentType, __callback) {
    // Execute AJAX
    await $.ajax({  
        type: method,  
        url: url, 
        data: dataObj,
        processData: processData,
        contentType: contentType,
        success: __callback
    });
}
</script>

<?php require($root."/includes/footer.php"); ?>