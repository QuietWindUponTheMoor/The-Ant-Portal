// Fetch data

// Send as form data
const form_data = new FormData();
form_data.append("login_status", login_status);
form_data.append("post_id", post_id);
$.ajax({  
    type: "POST",  
    url: API_addr + encodeURIComponent("fetch_post_data"), 
    data: form_data,
    processData: false,
    contentType: false,
    success: function (response) {
        // Process response
        const data = response;
        const status = data.status;
        const message = data.message;
        const details = data.details;

        /*
        Types:
        0 = Question
        1 = General
        2 = informative
        3 = observation
        4 = nuptial_flight
        */
       
        // Process data
        if (status === 200) { // OK
            // Get the post data
            const post = data.post_data;
            const user_id = post.userID;
            const type = post.type;
            const title = post.title;
            const body = post.body;
            let images = post.images;
            const views = post.views;
            const upvotes = post.upvotes;
            const downvotes = post.downvotes;
            const answers = post.answers;
            const time = post.time;
            const editedBy = post.editedBy;
            const editTime = post.editTime;
            let tags = post.tags;
            const latitude = post.lat;
            const longitude = post.long;
            const species = post.species;
            const temperature = post.temperature;
            const wind_speed = post.wind_speed;
            const moon_cycle = post.moon_cycle;
            
            // Answers data
            const answers_data = data.answers;
            answers_data.forEach(async answer => {
                const Answers = new ProcessAnswers(answer);
                await Answers.generateAnswer();
            });

            // Manage answer form
            if (parseInt(type) === 0) { // If the post is a question
                // Build form template
                const answer_form_template = 
                `
                <form class="form-main col main-object" id="answer-creation-form" action="" method="POST" enctype="multipart/form-data">
                    <div class="section col">
                        <p class="heading" id="heading">Answer This Question</p>
                        <p class="subheading" id="subheading"></p>
                    </div>
                    <div class="fields-list col" id="fields-list">
                        <div class="section col">
                            <label for="title">Your answer</label>
                            <textarea class="input-main" id="body-answer-input" name="body-answer-input" required></textarea>
                        </div>
                        <div class="section col">
                            <label for="select-images">Select Images (Optional) [Right-click to remove]</label>
                            <div class="section col" id="selected-images"></div>
                            <input class="hidden" type="file" id="image" name="image[]" accept="image/*" multiple/>
                            <button class="btn-action" type="button" id="select-images" onclick="$('#image').trigger('click');">Select Images (Optional)</button>
                        </div>
                        <input type="hidden" name="user_id" value="${thisUserID}" required/>
                        <div class="section col">
                            <button class="btn-secondary" type="button" id="cancel-post">Cancel Answer</button>
                            <button class="btn-main" type="submit">Submit Answer</button>
                        </div>
                    </div>
                </form>
                `;

                // Append the form if button is clicked
                $("#open-answer-form").on("click", () => {
                    // Append the form
                    $("#post-page-container").append(answer_form_template);
                    // Now scroll the client smoothly to the form
                    $("html, body").animate({
                        scrollTop: $("#answer-creation-form").offset().top
                    }, 1000);
                });
            } else { // If the post was not a question (anything else)
                $("#open-answer-form").remove();
            }

            // Manage easier items
            $("#post-type").text(calculatePostType(type));
            $("#vote-count").text(Math.floor(upvotes - downvotes));
            
            // Manage research items
            manageResearchItems(temperature, wind_speed, moon_cycle, longitude, latitude);

            // Manage tags
            tags = splitArray(tags);
            tags.forEach(tag => {
                const tag_template = `<a class="tag" href="/tags?search=${tag}" target="_blank">${tag}</a>`;
                $("#tags").append(tag_template);
            });

            // Set title and body
            // Title
            const types_without_using_species = [0, 1, 2];
            if (types_without_using_species.includes(type)) { // If post type doesn't need to use the species name
                $("#title").text(title);
            } else {
                const title_with_species = `${calculatePostType(type)}: ${species}`;
                $("#title").text(title_with_species);
            }
            $("#post-time").text(`Posted on ${timeCalc(time, "MM-DD-YYYY")} at ${timeCalc(time, "HH:MMa")}`);
            $("#body").html(body);

            // Set edit information
            const poster_info_template = 
            `
            <div class="action-container col" id="edit-info" onclick="window.location.assign('/users/1/user?user_id=${user_id}')">
                <div class="action-contents">
                    <div class="edit-info row highlighted">
                        <p class="label">Posted by</p>
                        <a class="user-link" id="posted-user" href="/users/1/user?user_id=${user_id}">Unknown User</a>
                    </div>
                </div>
            </div>
            `;
            const edit_info_template = 
            `
            <div class="action-container col" id="edit-info" onclick="window.location.assign('/users/1/user?user_id=${editedBy}')">
                <div class="action-contents">
                    <div class="edit-info row highlighted" id="edited-true">
                        <p class="label">Edited By</p>
                        <a class="user-link" id="edited-user" href="#">TestUser</a>
                        <p class="label">On</p>
                        <p class="edit-time" id="edit-time">${timeCalc(editTime, "MM-DD-YYYY HH:MMa")}</p>
                    </div>
                </div>
            </div>
            `;
            // Set user info first
            $(".post-object").find(".poster-info").append(poster_info_template);
            // Then set edit info
            if (editedBy !== 0 && editTime !== null) {
                $(".has-edit-info").append(edit_info_template);
            }

            // Set name color:
            $("#posted-user").css("color", acct_color);

            // Set view count
            $("#view-count").text(views + " views");
            // Set answer count
            $("#answer-count").text(answers + " answers");
            

            // Manage images
            if (images === null) {
                $("#images-container").hide();
            } else {
                images = splitArray(images);
                images.forEach(image => {
                    const template = `<div class="post-image-container"><a href="http://127.0.0.1:81/post_files/${image}" target="_blank"><img class="post-image" src="http://127.0.0.1:81/post_files/${image}"/></a></div>`;
                    $("#images-container").append(template);
                });
            }


            // Now check to see if the page needs an answers section
            const allowed_types_for_answers = [0];
            if (!allowed_types_for_answers.includes(type)) { // If this post doesn't require an answers section
                $("#answer-object").remove();
            }

            // Manage vote colors
            manageVoteCountColors();
        } else { // Failed to fetch data
            $("#title").text("Something went wrong fetching this post, please try again or contact an administrator.");
        }
    }
});

function splitArray(string) {
    return string.split(",");
}
function manageResearchItems(temperature, wind_speed, moon_cycle, longitude, latitude) {
    if (temperature !== null) {
        setResearchItems("#temperature", temperature);
    } else {
        $("#temperature").hide();
    }

    if (wind_speed !== null) {
        setResearchItems("#wind-speed", wind_speed);
    } else {
        $("#wind-speed").hide();
    }

    if (moon_cycle !== null) {
        setResearchItems("#moon-cycle", moon_cycle);
    } else {
        $("#moon-cycle").hide();
    }

    if (longitude !== null) {
        setResearchItems("#longitude", longitude);
    } else {
        $("#longitude").hide();
    }

    if (latitude !== null) {
        setResearchItems("#latitude", latitude);
    } else {
        $("#latitude").hide();
    }

    // If *ALL* of them are null
    if (
        temperature === null &&
        wind_speed === null &&
        moon_cycle === null &&
        longitude === null &&
        latitude === null
    ) {
        $("#research-details").hide();
    }
}
function setResearchItems(parent_id, data) {
    $(`#${parent_id} #data`).text(data);
}
function calculatePostType(type) {
    switch (type) {
        case 0:
            return "Question";
        case 1:
            return "General Post";
        case 2:
            return "Informative Post";
        case 3:
            return "Observation";
        case 4:
            return "Nuptial Flight Record";
        default:
            break;
    }
}
function manageVoteCountColors() {
    $(".vote-count").each(function() {
        // Get colors
        const secondBelow = "#FF0000";
        const firstBelow = "#A50133";
        const normal = "white";
        const first = "#B7CD00";
        const second = "#57CD00";
        const third = "#00FED0";
        const fourth = "#FE00EF";

        // Initialize color
        let color = normal; // Start it as 'normal' color first

        // Get vote value
        const val = parseInt($(this).text(), 10);

        // Set ranges
        const secondBelowZeroRange = val < -10;
        const firstBelowZeroRange = val >= -10 && val < 0;
        const normalRange = val === 0;
        const firstAboveRange = val > 0 && val < 5;
        const secondAboveRange = val >= 5 && val < 50;
        const thirdAboveRange = val >= 50 && val < 175;
        const fourthAboveRange = val >= 175;

        // Set the color
        if (fourthAboveRange) {
            color = fourth;
        } else if (thirdAboveRange) {
            color = third;
        } else if (secondAboveRange) {
            color = second;
        } else if (firstAboveRange) {
            color = first;
        } else if (normalRange) {
            color = normal;
        } else if (firstBelowZeroRange) {
            color = firstBelow;
        } else if (secondBelowZeroRange) {
            secondBelow;
        }

        $(this).css("color", color);
    });
}


class ProcessAnswers {
    answerID;
    forPostID;
    userID;
    postedByUsername = "Unknown User";
    body;
    time;
    images;
    upvotes;
    downvotes;
    editedBy;
    editedByUsername = "Unknown User";
    editTime;
    parentEl;

    constructor(answer_data) {
        this.answerID = answer_data.answerID;
        this.forPostID = answer_data.forPostID;
        this.userID = answer_data.userID;
        this.body = answer_data.body;
        this.time = answer_data.time;
        this.images = answer_data.images;
        this.upvotes = answer_data.upvotes;
        this.downvotes = answer_data.downvotes;
        this.editedBy = answer_data.editedBy;
        this.editTime = answer_data.editTime;
        this.parentEl = `#answer-${this.answerID}`;
    }

    async manageImages() {
        let images = this.images;
        if (images === null) {
            $("#images-container").hide();
        } else {
            images = splitArray(images);
            images.forEach(image => {
                const template = `<div class="post-image-container"><a href="http://127.0.0.1:81/answer_files/${image}" target="_blank"><img class="post-image" src="http://127.0.0.1:81/answer_files/${image}"/></a></div>`;
                this.el("#images-container").append(template);
            });
        }
    }

    async managePosterInfo() {
        try {
            // Get the postedBy username
            let postedByData = await query("SELECT username FROM users WHERE userID=?;", [this.userID]);
            postedByData = postedByData.data;
            $(`#posted-user-${this.answerID}`).text(postedByData.username);
        } catch (error) {
            console.error(`Error fetching answer's 'posted-by' information for answer ${this.answerID}: ${error}`);
        }
    }

    async manageEditSection() {
        try {
            if (this.editedBy !== 0 && this.editTime !== null) { // If the answer was actually edited
                // Get the editedBy username
                let editedByData = await query("SELECT username FROM users WHERE userID=?;", [this.userID]);
                editedByData = editedByData.data;

                // Build the edit info template
                const edit_info_template = 
                `
                <div class="action-container col" id="edit-info" onclick="window.location.assign('/users/1/user?user_id=${this.editedBy}')">
                    <div class="action-contents">
                        <div class="edit-info row highlighted" id="edited-true">
                            <p class="label">Edited By</p>
                            <a class="user-link" id="edited-user" href="/users/1/user?user_id=${this.editedBy}">${editedByData.username}</a>
                            <p class="label">On</p>
                            <p class="edit-time" id="edit-time">${timeCalc(this.editTime, "MM-DD-YYYY HH:MMa")}</p>
                        </div>
                    </div>
                </div>
                `;

                // Append to answer
                this.el(".has-edit-info").append(edit_info_template);
            }
        } catch (error) {
            console.error(`Error fetching answer's 'edited-by' information for answer ${this.answerID}: ${error}`);
        }
    }

    async generateAnswer() {
        // Build the answer template
        const answer_template = 
        `
        <div class="main-object row answer-object" id="answer-${this.answerID}">
            <div class="content col">
                <p class="content-header" id="post-type">ANSWER</p>

                <div class="post-actions row">
                    <div class="action-container col" id="voting">
                        <p class="action-title">Vote for this answer</p>
                        <div class="action-contents">
                            <div class="vote-image-container"><img class="vote-image" id="upvote-for-${this.answerID}" src="/web_images/icons/upvote.png"/></div>
                            <p class="vote-count" id="vote-count">${Math.floor(this.upvotes - this.downvotes)}</p>
                            <div class="vote-image-container"><img class="vote-image" id="downvote-for-${this.answerID}" src="/web_images/icons/downvote.png"/></div>
                        </div>
                    </div>
                </div>
                <div class="body-content col">
                    <p id="post-time">Answered on ${timeCalc(this.time, "MM-DD-YYYY")} at ${timeCalc(this.time, "HH:MMa")}</p>
                    <p class="post-text" id="body">${this.body}</p>
                </div>
                <div class="post-images" id="images-container"></div>
                <div class="post-actions row poster-info has-edit-info">
                    <div class="action-container col" id="edit-info" onclick="window.location.assign('/users/1/user?user_id=${this.userID}')">
                        <div class="action-contents">
                            <div class="edit-info row highlighted">
                                <p class="label">Posted by</p>
                                <a class="user-link" id="posted-user-${this.answerID}" href="/users/1/user?user_id=${this.userID}">${this.username}</a>
                            </div>
                        </div>
                    </div>
                    <!-- If the post has edit info, edit data will display here as well -->
                </div>
            </div>

            <div class="comments col" id="comments-container">
                <p class="comments-header">User Replies</p>
                <div class="replies-list col">
                    <div class="reply row">
                        <a class="user-link" href="#">TestAdmin</a>
                        <p class="comment-text">Lorem ipsum dolor sit amet.</p>
                    </div>
                    <div class="reply">
                        <a class="user-link" href="#">TestAdmin</a>
                        <p class="comment-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Commodi iste distinctio facilis. Quam.</p>
                    </div>
                </div>
                <div class="textbox-container col">
                    <label for="reply-textbox">Post a comment</label>
                    <div class="textbox-content row">
                        <input type="text" class="input-main" id="reply-textbox"/>
                        <button class="btn-action" type="button" id="create-reply">Reply</button>
                    </div>
                </div>
            </div>
        </div>
        `;

        // Append the answer
        $("#post-page-container").append(answer_template);

        // Finish processing
        await this.manageEditSection();
        await this.manageImages();
        await this.managePosterInfo();
    }
    
    el(identifier) {
        return $(this.parentEl).find(identifier);
    }
}