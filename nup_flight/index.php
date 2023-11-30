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
                        <p class="post-title" id="title">Nuptial Flight #<?php echo $flightID; ?>: <?php echo $species; ?></p>
                    </div>
                    <div class="post-section post-flagging">
                        <?php echo $flagging_modal; ?>
                    </div>
                    <div class="post-section vote-container">
                        <div class="vote-subcontainer">
                            <div class="icon-container" title="Upvote this post"><img class="icon" id="upvote-trigger" style="<?php echo $upvoteButtonOpacity; ?>" src="/web_images/icons/upvote.png"/></div>
                            <p class="vote-count <?php echo $color; ?>" id="total-votes"><?php echo $totalVotes; ?></p>
                            <div class="icon-container" title="Downvote this post"><img class="icon" id="downvote-trigger" style="<?php echo $downvoteButtonOpacity; ?>" src="/web_images/icons/downvote.png"/></div>
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
                    <div class="post-section section-wrap post-meta">
                        <p class="meta">Temperature: <?php echo $tempF; ?> (<?php echo $tempC; ?>)</p>
                        <p class="meta">Wind Speed: <?php echo $windSpeed; ?>mph</p>
                        <p class="meta">Moon Cycle: <?php echo $moonCycle; ?></p>
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
                            $repliesRes = $db->select("SELECT * FROM replies WHERE for_type=? AND forItemID=?;", "si", "post", $flightID);
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
const user_data = {
    id: <?php echo $userID; ?>,
    username: "<?php echo $thisUsersUsername; ?>",
};
// Post
const post_data = {
    db: "<?php echo $dbHost; ?>",
    postType: 4, // 4 = nuptial_flight
    postID: <?php echo $flightID; ?>,
};



// Replies
// Initialize current_val:
let current_textbox_val = "";
async function submitReply(forType, $this) {
    // Prevent default actions
    event.preventDefault();

    // Get comment thread
    $comment_thread = $this.closest(".comments-box-main").find(".comments-list");
    // Get textbox
    $textbox = $this.closest(".textarea-container").find(".comment-textbox");

    // Fetch reply text
    const reply_text = $textbox.val();

    // Get user's date
    const userDate = new Date();
    const options = { timeZone: 'America/Chicago' };
    const month = (userDate.getMonth() + 1).toString().padStart(2, '0');
    const day = userDate.getDate().toString().padStart(2, '0');
    const year = userDate.getFullYear();
    const hours = userDate.getHours() % 12 || 12;
    const minutes = userDate.getMinutes().toString().padStart(2, '0');
    const period = userDate.getHours() < 12 ? 'AM' : 'PM';
    const timeZone = new Intl.DateTimeFormat('en-US', { timeZoneName: 'short', timeZone: options.timeZone }).format(userDate).split(' ').pop();
    const timeString = `${month}/${day}/${year} @ ${hours}:${minutes}${period} (${timeZone})`;

    // Initialize reply template
    const reply_template =
    `
    <div class="comment-container">
        <div class="comment-options post-flagging">
            <?php echo $flagging_modal; ?>
        </div>
        <div class="comment-content">
            <a class="user-link" href="/users/user?userID=<?php echo $userID; ?>"><?php echo $thisUsersUsername; ?></a>
            <p class="comment">${reply_text}</p>
            <p class="comment comment_date">${timeString}</p>
        </div>
    </div>
    `;
    
    // Get reply data
    const reply_data = {
        db: "<?php echo $dbHost; ?>",
        userID: <?php echo $userID; ?>,
        forType: forType,
        itemID: <?php echo $flightID; ?>,
        text: reply_text,
        datetime: timeString,
    };

    // Submit the reply
    await $.ajax({  
        type: "POST",  
        url: "/php/lib/replies/create_reply.php", 
        data: reply_data,
        success: function (response) {
            response = parseInt(response);
            if (response === 1) {
                $comment_thread.append(reply_template);
                $textbox.val("");
                // Find the parent of the textbox with class 'textarea-container'
                $textarea_container = $textbox.closest(".textarea-container");
                // Reverse the classes for it
                $textarea_container.addClass("textarea-container-inactive").removeClass("textarea-container-active");
                // Textbox placeholder change
                $textbox.attr("placeholder", "Add Comment");
                // Reset current_textbox_val
                current_textbox_val = "";
            } else {
                // Save the current tetbox value just in case
                current_textbox_val = $textbox.val();
                // Display error to user via textbox
                $textbox.val(response);
            }
        }
    });
}

// Reply textboxes
$(".comment-textbox").on("input", function () {
    $(this).css("height", "auto").css("height", this.scrollHeight + "px");
});
$(".textarea-container").on("click", function (event) {
    // Prevent click propagation
    event.stopPropagation();

    // Add active class, remove inactive class
    $(this).addClass("textarea-container-active").removeClass("textarea-container-inactive");

    // Assign $this variable as $(this) to target a specific element later
    $this = $(this);
    
    // Get current textbox
    $textbox = $this.find(".comment-textbox");

    // Textbox placeholder change
    $textbox.attr("placeholder", "Write your comment here. Please refrain from needless comments.");

    if ($textbox.val()) {
        // Textbox has text in it
        // Save the current text in the box:
        current_textbox_val = $textbox.val();
    } else {
        // Textbox does not have text in it
        // Re-assign the old text if there was any
        $textbox.val(current_textbox_val);
        // Assign current textbox value
        current_textbox_val = $textbox.val();
    }

    // If the above is already done, and the user clicks outside of this element, reverse the classes
    $(document).on("click", () => {
        // Reverse the classes to original
        $this.addClass("textarea-container-inactive").removeClass("textarea-container-active");
        // Textbox placeholder change
        $textbox.attr("placeholder", "Add Comment");

        if ($textbox.val()) {
            // Textbox has text in it
            // Save the current text in the box:
            current_textbox_val = $textbox.val();
        }
        
        // Remove the text from the textbox
        $textbox.val("");
    });
});

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
$(".flag-image").on("click", function () {
    let modal = $(this).next(".flagging-modal");

    // Apply styles to the modal or flag image
    modal.css("display", "flex").hide().fadeIn();
});
$(".cancel-flagging").on("click", () => {
    $(".flagging-modal").hide();
});

// Change image color to red upon hover of flag button
$(".flag-image").on("mouseenter", function () {
    $(this).attr("src", "/web_images/icons/flag_red.png");
});
$(".flag-image").on("mouseleave", function () {
    $(this).attr("src", "/web_images/icons/flag.png");
});

// Upvote triggers
$("#upvote-trigger").on("click", async () => {
    if ($isLoggedIn === 1) {
        await upvote();
    } else {
        alert("Sorry, but you must be logged in to vote.");
    }
});
$("#downvote-trigger").on("click", async () => {
    if ($isLoggedIn === 1) {
        await downvote();
    } else {
        alert("Sorry, but you must be logged in to vote.");
    }
});

if ($isLoggedIn !== 1) {
    $(".post-flagging").hide();
    $(".comment-box-write").hide();
}

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
<script type="text/javascript" src="/js/lib/editing/editing.js"></script>

<?php require($root."/includes/footer.php"); ?>