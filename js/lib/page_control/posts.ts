// JQuery extensions
interface JQuery {
    voteControl($isLoggedIn: number | boolean): JQuery;
    changeAttrOnHover(attrType: string, attrValOnHover: string, attrValWithoutHover: string): JQuery;
    renderLineBreak(): JQuery;
    convertToEditable(): JQuery;
    submitSuggestion(url: string, method: string): any;
    getNodeType(): string | undefined;
    onSuccess(callback: (response: any) => boolean): this;
}
// Methods to extend
function voteControl($isLoggedIn: number | boolean): JQuery {
    $(this).on("click", function () {
        const ID: string = $(this).attr("id");
        if (ID === "upvote-trigger") {
            // User upvoted
            if ($isLoggedIn === 1) {
                upvote();
            } else {
                alert("Sorry, but you must be logged in to vote.");
            }
        } else if (ID === "downvote-trigger") {
            // User downvoted
            if ($isLoggedIn === 1) {
                downvote();
            } else {
                alert("Sorry, but you must be logged in to vote.");
            }
        } else {
            console.error(`Invalid element on ID '${ID}'`);
        }
    });

    return this;
}
function changeAttrOnHover(attrType: string, attrValOnHover: string, attrValWithoutHover: string): JQuery {
    this.on("mouseenter", function () {
        $(this).attr(attrType, attrValOnHover);
    });
    this.on("mouseleave", function () {
        $(this).attr(attrType, attrValWithoutHover);
    });

    return this;
}
function renderLineBreak(): JQuery {
    return this.html().replace(/<br\s*[/]?>/gi, "\n");
}
function convertToEditable(): JQuery {
    // Initializations
    let saved_text: string = "";
    let container: JQuery<HTMLElement>;
    let element: JQuery<HTMLElement>;
    let typeObject: any;

    // Get the ID of the element
    const ID: string = this.attr("id");
    // Save the element's contents
    saved_text = this.renderLineBreak();

    // Get append type
    interface Appends {
        [key: string]: {
            element: JQuery<HTMLElement>,
            label: string,
            elementID: string,
            container: JQuery<HTMLElement>,
            attributes: {name: string, value: string}[],
        };
    }
    const appends: Appends = {
        title: {
            element: $("<input />") as JQuery<HTMLElement>,
            label: '<label for="title-editing-mode">Suggest an edit to the post\'s title</label>',
            elementID: "title-editing-mode",
            container: $("#title-container"),
            attributes: [
                {name: "class", value: "text-editing-mode"},
                {name: "minlength", value: "5"},
                {name: "maxlength", value: "256"},
                {name: "required", value: "true"},
            ]
        },
        body: {
            element: $("<textarea></textarea>") as JQuery<HTMLElement>,
            label: '<label for="title-editing-mode">Suggest an edit to the post\'s body</label>',
            elementID: "body-editing-mode",
            container: $(".body-container"),
            attributes: [
                {name: "class", value: "body"},
                {name: "class", value: "text-editing-mode"},
                {name: "minlength", value: "5"},
                {name: "maxlength", value: "30000"},
                {name: "required", value: "true"},
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
            // Do regex for title to get the number (post ID) & the species name
            const regex: RegExp = /^.*?#(\d+):\s*([^:]+)$/;
            let match: object = saved_text.match(regex);
            let species: string = match[2];
            let postID: number = parseInt(match[1]);
            saved_text = species;
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
            console.error(`Invalid element on ID '${ID}'`);
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
    typeObject.attributes.forEach(attr => {
        if (attr.name === "class") {
            element.addClass(attr.value);
        } else {
            element.attr(attr.name, attr.value);
        }
    });

    // Now assign it the proper value
    element.val(saved_text);

    // Return item
    return this; // move this to onSuccess() later
}
function submitSuggestion(url: string, method: string): JQuery {
    /*
    use case of this function:
    $("#test").submitSuggestion("url", "test", "POST", false, false).onSuccess(response => {
        console.log(response);
    });
    */

    // Get element type
    const elementType: string = this.getNodeType();
    this.on("click", () => {
        if (elementType === "button") {
            // Process data
            let newTitle: string | any = globalTitle.val();
            let newBody: string | any = globalBody.val();
            newTitle = `Nuptial Flight #${globalPostID}: ${newTitle}`;
            newBody = newBody.replace(/\n/g, "<br>");
            let byUserID: number = user_data.id;
            let postType: number = post_data.postType;
            let postID: number = post_data.postID;
            let db: string = post_data.db;
        
            let dataObj: object = {
                db: db,
                userID: byUserID,
                postType: postType,
                postID: postID,
                newTitle: newTitle,
                newBody: newBody,
            };

            // Execute AJAX
            $.ajax({  
                type: method,  
                url: url, 
                data: dataObj,
                success: (response) => {
                    if (this.data("onSuccessCallback")) {
                        this.data("onSuccessCallback")(response);
                    }
                },
            });
        } else {
            console.error("The element: ", this[0], "is not the correct type of element to call submitSuggestion on. submitSuggestion must be called on a JQuery object of type 'BUTTON'.");
        }
    });

    return this;
}
function getNodeType(): string | undefined {
    return this.prop("nodeName").toLowerCase();
}
// Extend the methods via JQuery
$.fn.extend({
    voteControl: voteControl,
    changeAttrOnHover: changeAttrOnHover,
    renderLineBreak: renderLineBreak,
    convertToEditable: convertToEditable,
    submitSuggestion: submitSuggestion,
    onSuccess: function(callback) {
        this.data("onSuccessCallback", callback);
        return this;
    },
    getNodeType: getNodeType
});




// Declarations
declare const $isLoggedIn: number | boolean;
declare const $flaggingModal: string;
declare const user_data: {
    id: number;
    username: string;
};
declare const post_data: {
    db: string;
    postType: number;
    postID: number;
};

// Initializations
let current_textbox_val: string | any = "";
let globalPostID: number;
let globalTitle: JQuery<HTMLElement>;
let globalBody: JQuery<HTMLElement>;

// Constants
const $flagging_response: JQuery<HTMLElement> = $("#flagging-response");
const $radioContainers: JQuery<HTMLElement> = $(".radio-section");

// On page load
if ($isLoggedIn !== 1) {
    // Hide elements that required login status as 1/true
    $(".post-flagging").hide();
    $(".comment-box-write").hide();
}


// Listeners
// Editing
$("#start-editing").on("click", () => {
    // Static vars
    const controls_container: JQuery<HTMLElement> = $("#control-buttons");

    const elementsToConvert: Array<JQuery<HTMLElement>> = [
        $("#title"),
        $(".body"),
    ];

    // Convert body
    elementsToConvert.forEach(element => {
        element.convertToEditable();
    });

    const elementsToRemove: Array<JQuery<HTMLElement>> = [
        $(".post-flagging"),
        $(".vote-container"),
        $("#user-data-section"),
        $(".images-section"),
        $(".post-meta"),
        $("#start-editing-container"),
        $(".post-page-comments")
    ];

    // Remove needless elements
    elementsToRemove.forEach(element => {
        element.hide();
    });

    // Lastly, show the confirmation/cancel buttons
    $("#cancel-edits").css("display", "flex");
    $("#finish-edits").css("display", "flex");

    $("#cancel-edits").on("click", () => {
        window.location.reload();
    });
});
$("#finish-edits").submitSuggestion("/php/lib/posts/edit_suggestions.php", "POST").onSuccess(response => {
    // Handle response here
    if (parseInt(response) === 1) {
        // Success
        window.location.reload();
    } else {
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
    const $this: JQuery<HTMLElement> = $(this);
    
    // Get current textbox
    const $textbox: JQuery<HTMLElement> = $this.find(".comment-textbox");

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

// Submitting flags
$("#flagging-form").on("submit", function (event) {
    // Prevent default actions
    event.preventDefault();
    // Get form data
    const formData: any = new FormData(this as HTMLFormElement);

    // Assign response interface
    interface Response {
        code: number | any,
        reason: string,
        reported_from: string,
        report_explanation: string,
        success: string,
        error: string,
    }

    // Submit the form
    sendAJAX("/php/lib/flagging/submit_report.php", formData, "POST", function (unparsedResponse) {
        const response: Response = JSON.parse(unparsedResponse);
        const resCode: number | any = parseInt(response.code);
        const reason: string = response.reason;
        const link: string = response.reported_from;
        const explanation: string = response.report_explanation;
        const success: string = response.success;
        const error: string = response.error;
        if (resCode === 1) {
            // Success
            this.reset();
            $radioContainers.hide();
            $("#submit-flagging").hide();
            $(".explanation-section").hide();
            $("#cancel-flagging").text("Finished");
            $flagging_response.html(success).addClass("success");
        } else {
            // Error
            $flagging_response.html(error).addClass("error");
        }
    });
});

// Resizing
let sectionWidth: number | any = $(".post-flagging").width();
$(".flagging-modal").css("width", Math.floor(sectionWidth)).css("max-width", Math.floor(sectionWidth) + "px");
$(window).on("resize", () => {
    let sectionWidth: number | any = $(".post-flagging").width();
    $(".flagging-modal").css("width", Math.floor(sectionWidth)).css("max-width", Math.floor(sectionWidth) + "px");
});

// Selected flagging reasons controls
$radioContainers.find(":radio").on("change", event => {
    $radioContainers.removeClass("radio-active"); // remove from all containers
    $(event.target).closest(".radio-section").addClass("radio-active"); // add class to current
});

// Opening/closing the flagging modal
$(".flag-image").on("click", function () {
    let modal: JQuery<HTMLElement> = $(this).next(".flagging-modal");

    // Apply styles to the modal or flag image
    modal.css("display", "flex").hide().fadeIn();
});
$(".cancel-flagging").on("click", () => {
    $(".flagging-modal").hide();
});

// Hover events
$(".flag-image").changeAttrOnHover("src", "/web_images/icons/flag_red.png", "/web_images/icons/flag.png");
$(".control-button").changeAttrOnHover("src", "/web_images/icons/editing_active.png", "/web_images/icons/editing.png");

// Upvote triggers
$(".vote-image").voteControl($isLoggedIn);




// Helper funcitons
function sendAJAX(url: string, dataObj: object, method: string, __callback: any): any {
    // Execute AJAX
    $.ajax({  
        type: method,  
        url: url, 
        data: dataObj,
        success: __callback
    });
}
function upvote(): any {
    $("#upvote-trigger").css("opacity", "0.6");
    $("#downvote-trigger").css("opacity", "0.6");
    $.ajax({  
        type: "POST",  
        url: "/php/lib/voting/post_voting.php", 
        data: {
            voteType: 2,
            postID: post_data.postID,
            userID: user_data.id,
            upvoteOrDownvote: 1
        },
        success: function(response) {
            // ParseInt--if parseInt DOESN'T result in "NaN", then it was a success.
            response = parseInt(response);
            if (isNaN(response)) {
                // Not successful
                alert(response);
            } else if (!isNaN(response)) {
                $("#total-votes").text(response);
                $("#upvote-trigger").css("opacity", "1");
            } else {
                alert(response);
            }
        }
    });
}
function downvote(): any {
    $("#upvote-trigger").css("opacity", "0.6");
    $("#downvote-trigger").css("opacity", "0.6");
    $.ajax({  
        type: "POST",  
        url: "/php/lib/voting/post_voting.php", 
        data: {
            voteType: 2,
            postID: post_data.postID,
            userID: user_data.id,
            upvoteOrDownvote: 2
        },
        success: function(response) {
            // ParseInt--if parseInt DOESN'T result in "NaN", then it was a success.
            response = parseInt(response);
            if (isNaN(response)) {
                // Not successful
                alert(response);
            } else if (!isNaN(response)) {
                $("#total-votes").text(response);
                $("#downvote-trigger").css("opacity", "1");
            } else {
                alert(response);
            }
        }
    });
}
function submitReply(forType: string, $this: JQuery<HTMLElement>): any {
    // Get comment thread
    const $comment_thread: JQuery<HTMLElement> = $this.closest(".comments-box-main").find(".comments-list");
    // Get textbox
    const $textbox: JQuery<HTMLElement> = $this.closest(".textarea-container").find(".comment-textbox");

    // Fetch reply text
    const reply_text: string | any = $textbox.val();

    // Get user's date
    const userDate: any = new Date();
    const options: any = { timeZone: 'America/Chicago' };
    const month: any = (userDate.getMonth() + 1).toString().padStart(2, '0');
    const day: any = userDate.getDate().toString().padStart(2, '0');
    const year: any = userDate.getFullYear();
    const hours: any = userDate.getHours() % 12 || 12;
    const minutes: any = userDate.getMinutes().toString().padStart(2, '0');
    const period: any = userDate.getHours() < 12 ? 'AM' : 'PM';
    const timeZone: any = new Intl.DateTimeFormat('en-US', { timeZoneName: 'short', timeZone: options.timeZone }).format(userDate).split(' ').pop();
    const timeString: string = `${month}/${day}/${year} @ ${hours}:${minutes}${period} (${timeZone})`;

    // Initialize reply templates
    const reply_template: string =
    `
    <div class="comment-container">
        <div class="comment-options post-flagging">
            ${$flaggingModal}
        </div>
        <div class="comment-content">
            <a class="user-link" href="/users/user?userID=<?php echo $userID; ?>">${user_data.username}</a>
            <p class="comment">${reply_text}</p>
            <p class="comment comment_date">${timeString}</p>
        </div>
    </div>
    `;

    // Interface for reply_data
    interface ReplyData {
        db: string,
        userID: number,
        forType: string,
        itemID: number,
        text: string,
        datetime: string,
    }
    
    // Get reply data
    const reply_data = {
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
                let $textarea_container: JQuery<HTMLElement> = $textbox.closest(".textarea-container");
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