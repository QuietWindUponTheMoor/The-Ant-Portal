// Methods to extend
function voteControl($isLoggedIn) {
    $(this).on("click", function () {
        var ID = $(this).attr("id");
        if (ID === "upvote-trigger") {
            // User upvoted
            if ($isLoggedIn === 1) {
                upvote();
            }
            else {
                alert("Sorry, but you must be logged in to vote.");
            }
        }
        else if (ID === "downvote-trigger") {
            // User downvoted
            if ($isLoggedIn === 1) {
                downvote();
            }
            else {
                alert("Sorry, but you must be logged in to vote.");
            }
        }
        else {
            console.error("Invalid element on ID '".concat(ID, "'"));
        }
    });
    return this;
}
function changeAttrOnHover(attrType, attrValOnHover, attrValWithoutHover) {
    this.on("mouseenter", function () {
        $(this).attr(attrType, attrValOnHover);
    });
    this.on("mouseleave", function () {
        $(this).attr(attrType, attrValWithoutHover);
    });
    return this;
}
function renderLineBreak() {
    return this.html().replace(/<br\s*[/]?>/gi, "\n");
}
function convertToEditable() {
    // Initializations
    var saved_text = "";
    var container;
    var element;
    var typeObject;
    // Get the ID of the element
    var ID = this.attr("id");
    // Save the element's contents
    saved_text = this.renderLineBreak();
    var appends = {
        title: {
            element: $("<input />"),
            label: '<label for="title-editing-mode">Suggest an edit to the post\'s title</label>',
            elementID: "title-editing-mode",
            container: $("#title-container"),
            attributes: [
                { name: "class", value: "text-editing-mode" },
                { name: "minlength", value: "5" },
                { name: "maxlength", value: "256" },
                { name: "required", value: "true" },
            ]
        },
        body: {
            element: $("<textarea></textarea>"),
            label: '<label for="title-editing-mode">Suggest an edit to the post\'s body</label>',
            elementID: "body-editing-mode",
            container: $(".body-container"),
            attributes: [
                { name: "class", value: "body" },
                { name: "class", value: "text-editing-mode" },
                { name: "minlength", value: "5" },
                { name: "maxlength", value: "30000" },
                { name: "required", value: "true" },
            ]
        },
    };
    switch (ID) {
        case "title":
            // Assign the appends item as new variable
            typeObject = appends.title;
            // Assign the element
            element = typeObject.element;
            element.attr("id", typeObject.id);
            // Get the element's container
            container = typeObject.container;
            var postID = void 0;
            if (post_data.postType === 4) {
                // Do regex for title to get the number (post ID) & the species name
                var regex = /^.*?#(\d+):\s*([^:]+)$/;
                var match = saved_text.match(regex);
                var species = match[2];
                postID = parseInt(match[1]);
                saved_text = species;
            }
            // Now assign global variabls 'postID' and 'species' and 'title':
            globalPostID = postID;
            globalTitle = element;
            break;
        case "body":
            // Assign the appends item as new variable
            typeObject = appends.body;
            // Assign the element
            element = typeObject.element;
            element.attr("id", typeObject.id);
            // Get the element's container
            container = typeObject.container;
            // Assign global body
            globalBody = element;
            break;
        default:
            console.error("Invalid element on ID '".concat(ID, "'"));
            break;
    }
    // Now remove the element
    this.remove();
    // Change the container's class
    container.addClass("section-wrap");
    // Append the element & assign it the proper attributes
    // Append the label first
    container.append(typeObject.label);
    container.append(element);
    typeObject.attributes.forEach(function (attr) {
        if (attr.name === "class") {
            element.addClass(attr.value);
        }
        else {
            element.attr(attr.name, attr.value);
        }
    });
    // Now assign it the proper value
    element.val(saved_text);
    // Return item
    return this; // move this to onSuccess() later
}
function submitSuggestion(url, method) {
    /*
    use case of this function:
    $("#test").submitSuggestion("url", "POST").onSuccess(response => {
        console.log(response);
    });
    */
    var _this = this;
    // Get element type
    var elementType = this.getNodeType();
    this.on("click", function () {
        if (elementType === "button") {
            // Process data
            // Initialize dataObj
            var dataObj = void 0;
            if (post_data.postType === 4) {
                var newTitle = globalTitle.val();
                var newBody = globalBody.val();
                newTitle = "Nuptial Flight #".concat(globalPostID, ": ").concat(newTitle);
                newBody = newBody.replace(/\n/g, "<br>");
                var byUserID = user_data.id;
                var postType = post_data.postType;
                var postID = post_data.postID;
                var db = post_data.db;
                dataObj = {
                    db: db,
                    userID: byUserID,
                    postType: postType,
                    postID: postID,
                    newTitle: newTitle,
                    newBody: newBody,
                };
            }
            else {
                var newTitle = globalTitle.val();
                var newBody = globalBody.val();
                newBody = newBody.replace(/\n/g, "<br>");
                var byUserID = user_data.id;
                var postType = post_data.postType;
                var postID = post_data.postID;
                var db = post_data.db;
                dataObj = {
                    db: db,
                    userID: byUserID,
                    postType: postType,
                    postID: postID,
                    newTitle: newTitle,
                    newBody: newBody,
                };
            }
            // Execute AJAX
            $.ajax({
                type: method,
                url: url,
                data: dataObj,
                success: function (response) {
                    if (_this.data("onSuccessCallback")) {
                        _this.data("onSuccessCallback")(response);
                    }
                },
            });
        }
        else {
            console.error("The element: ", _this[0], "is not the correct type of element to call submitSuggestion on. submitSuggestion must be called on a JQuery object of type 'BUTTON'.");
        }
    });
    return this;
}
function getNodeType() {
    return this.prop("nodeName").toLowerCase();
}
// Extend the methods via JQuery
$.fn.extend({
    voteControl: voteControl,
    changeAttrOnHover: changeAttrOnHover,
    renderLineBreak: renderLineBreak,
    convertToEditable: convertToEditable,
    submitSuggestion: submitSuggestion,
    onSuccess: function (callback) {
        this.data("onSuccessCallback", callback);
        return this;
    },
    getNodeType: getNodeType
});
// Initializations
var current_textbox_val = "";
var globalPostID;
var globalTitle;
var globalBody;
// Constants
var $flagging_response = $("#flagging-response");
var $radioContainers = $(".radio-section");
// On page load
if ($isLoggedIn !== 1) {
    // Hide elements that required login status as 1/true
    $(".post-flagging").hide();
    $(".comment-box-write").hide();
}
// Listeners
// Editing
$("#start-editing").on("click", function () {
    // Static vars
    var controls_container = $("#control-buttons");
    var elementsToConvert = [
        $("#title"),
        $(".body"),
    ];
    // Convert body
    elementsToConvert.forEach(function (element) {
        element.convertToEditable();
    });
    var elementsToRemove = [
        $(".post-flagging"),
        $(".vote-container"),
        $("#user-data-section"),
        $(".images-section"),
        $(".post-meta"),
        $("#start-editing-container"),
        $(".post-page-comments")
    ];
    // Remove needless elements
    elementsToRemove.forEach(function (element) {
        element.hide();
    });
    // Lastly, show the confirmation/cancel buttons
    $("#cancel-edits").css("display", "flex");
    $("#finish-edits").css("display", "flex");
    $("#cancel-edits").on("click", function () {
        window.location.reload();
    });
});
$("#finish-edits").submitSuggestion("/php/lib/posts/edit_suggestions.php", "POST").onSuccess(function (response) {
    // Handle response here
    if (parseInt(response) === 1) {
        // Success
        window.location.reload();
    }
    else {
        // Error
        console.error(response);
    }
});
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
    var $this = $(this);
    // Get current textbox
    var $textbox = $this.find(".comment-textbox");
    // Textbox placeholder change
    $textbox.attr("placeholder", "Write your comment here. Please refrain from needless comments.");
    if ($textbox.val()) {
        // Textbox has text in it
        // Save the current text in the box:
        current_textbox_val = $textbox.val();
    }
    else {
        // Textbox does not have text in it
        // Re-assign the old text if there was any
        $textbox.val(current_textbox_val);
        // Assign current textbox value
        current_textbox_val = $textbox.val();
    }
    // If the above is already done, and the user clicks outside of this element, reverse the classes
    $(document).on("click", function () {
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
// Submitting flags
$("#flagging-form").on("submit", function (event) {
    // Prevent default actions
    event.preventDefault();
    // Get form data
    var formData = new FormData(this);
    // Submit the form
    sendAJAX("/php/lib/flagging/submit_report.php", formData, "POST", function (unparsedResponse) {
        var response = JSON.parse(unparsedResponse);
        var resCode = parseInt(response.code);
        var reason = response.reason;
        var link = response.reported_from;
        var explanation = response.report_explanation;
        var success = response.success;
        var error = response.error;
        if (resCode === 1) {
            // Success
            this.reset();
            $radioContainers.hide();
            $("#submit-flagging").hide();
            $(".explanation-section").hide();
            $("#cancel-flagging").text("Finished");
            $flagging_response.html(success).addClass("success");
        }
        else {
            // Error
            $flagging_response.html(error).addClass("error");
        }
    });
});
// Resizing
var sectionWidth = $(".post-flagging").width();
$(".flagging-modal").css("width", Math.floor(sectionWidth)).css("max-width", Math.floor(sectionWidth) + "px");
$(window).on("resize", function () {
    var sectionWidth = $(".post-flagging").width();
    $(".flagging-modal").css("width", Math.floor(sectionWidth)).css("max-width", Math.floor(sectionWidth) + "px");
});
// Selected flagging reasons controls
$radioContainers.find(":radio").on("change", function (event) {
    $radioContainers.removeClass("radio-active"); // remove from all containers
    $(event.target).closest(".radio-section").addClass("radio-active"); // add class to current
});
// Opening/closing the flagging modal
$(".flag-image").on("click", function () {
    var modal = $(this).next(".flagging-modal");
    // Apply styles to the modal or flag image
    modal.css("display", "flex").hide().fadeIn();
});
$(".cancel-flagging").on("click", function () {
    $(".flagging-modal").hide();
});
// Hover events
$(".flag-image").changeAttrOnHover("src", "/web_images/icons/flag_red.png", "/web_images/icons/flag.png");
$(".control-button").changeAttrOnHover("src", "/web_images/icons/editing_active.png", "/web_images/icons/editing.png");
// Upvote triggers
$(".vote-image").voteControl($isLoggedIn);
// Helper funcitons
function sendAJAX(url, dataObj, method, __callback) {
    // Execute AJAX
    $.ajax({
        type: method,
        url: url,
        data: dataObj,
        success: __callback
    });
}
function upvote() {
    // Initialize vote/post type:
    var voteType;
    if (post_data.postType === 4) {
        voteType = 2;
    }
    else {
        voteType = 1;
    }
    $("#upvote-trigger").css("opacity", "0.6");
    $("#downvote-trigger").css("opacity", "0.6");
    $.ajax({
        type: "POST",
        url: "/php/lib/voting/post_voting.php",
        data: {
            voteType: voteType,
            postID: post_data.postID,
            userID: user_data.id,
            upvoteOrDownvote: 1
        },
        success: function (response) {
            // ParseInt--if parseInt DOESN'T result in "NaN", then it was a success.
            var parsedResponse = parseInt(response);
            if (isNaN(parsedResponse)) {
                // Not successful
                console.error(response);
            }
            else if (!isNaN(parsedResponse)) {
                $("#total-votes").text(response);
                $("#downvote-trigger").css("opacity", "1");
            }
            else {
                console.error(response);
            }
        }
    });
}
function downvote() {
    // Initialize vote/post type:
    var voteType;
    if (post_data.postType === 4) {
        voteType = 2;
    }
    else {
        voteType = 1;
    }
    $("#upvote-trigger").css("opacity", "0.6");
    $("#downvote-trigger").css("opacity", "0.6");
    $.ajax({
        type: "POST",
        url: "/php/lib/voting/post_voting.php",
        data: {
            voteType: voteType,
            postID: post_data.postID,
            userID: user_data.id,
            upvoteOrDownvote: 2
        },
        success: function (response) {
            // ParseInt--if parseInt DOESN'T result in "NaN", then it was a success.
            var parsedResponse = parseInt(response);
            if (isNaN(parsedResponse)) {
                // Not successful
                console.error(response);
            }
            else if (!isNaN(parsedResponse)) {
                $("#total-votes").text(response);
                $("#downvote-trigger").css("opacity", "1");
            }
            else {
                console.error(response);
            }
        }
    });
}
function submitReply(forType, $this) {
    // Get comment thread
    var $comment_thread = $this.closest(".comments-box-main").find(".comments-list");
    // Get textbox
    var $textbox = $this.closest(".textarea-container").find(".comment-textbox");
    // Fetch reply text
    var reply_text = $textbox.val();
    // Get user's date
    var userDate = new Date();
    var options = { timeZone: 'America/Chicago' };
    var month = (userDate.getMonth() + 1).toString().padStart(2, '0');
    var day = userDate.getDate().toString().padStart(2, '0');
    var year = userDate.getFullYear();
    var hours = userDate.getHours() % 12 || 12;
    var minutes = userDate.getMinutes().toString().padStart(2, '0');
    var period = userDate.getHours() < 12 ? 'AM' : 'PM';
    var timeZone = new Intl.DateTimeFormat('en-US', { timeZoneName: 'short', timeZone: options.timeZone }).format(userDate).split(' ').pop();
    var timeString = "".concat(month, "/").concat(day, "/").concat(year, " @ ").concat(hours, ":").concat(minutes).concat(period, " (").concat(timeZone, ")");
    // Initialize reply templates
    var reply_template = "\n    <div class=\"comment-container\">\n        <div class=\"comment-options post-flagging\">\n            ".concat($flaggingModal, "\n        </div>\n        <div class=\"comment-content\">\n            <a class=\"user-link\" href=\"/users/user?userID=<?php echo $userID; ?>\">").concat(user_data.username, "</a>\n            <p class=\"comment\">").concat(reply_text, "</p>\n            <p class=\"comment comment_date\">").concat(timeString, "</p>\n        </div>\n    </div>\n    ");
    // Get reply data
    var reply_data = {
        db: post_data.db,
        userID: user_data.id,
        forType: forType,
        itemID: post_data.postID,
        text: reply_text,
        datetime: timeString,
    };
    // Submit the reply
    $.ajax({
        type: "POST",
        url: "/php/lib/replies/create_reply.php",
        data: reply_data,
        success: function (response) {
            response = parseInt(response);
            if (response === 1) {
                $comment_thread.append(reply_template);
                $textbox.val("");
                // Find the parent of the textbox with class 'textarea-container'
                var $textarea_container = $textbox.closest(".textarea-container");
                // Reverse the classes for it
                $textarea_container.addClass("textarea-container-inactive").removeClass("textarea-container-active");
                // Textbox placeholder change
                $textbox.attr("placeholder", "Add Comment");
                // Reset current_textbox_val
                current_textbox_val = "";
            }
            else {
                // Save the current tetbox value just in case
                current_textbox_val = $textbox.val();
                // Display error to user via textbox
                $textbox.val(response);
            }
        }
    });
}
