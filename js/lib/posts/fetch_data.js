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
            $("#post-time").text(`Posted on ${timeCalc(time, "MM-DD-YYYY HH:MMa")}`);
            $("#body").html(body);

            // Set edit information
            const poster_info_template = 
            `
            <div class="action-container col" id="edit-info" onclick="window.location.assign('/users/1/user?user_id=${user_id}')">
                <div class="action-contents">
                    <div class="edit-info row highlighted" id="edited-true">
                        <p class="label">Posted by</p>
                        <a class="user-link" id="posted-user" href="#">TestUser</a>
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
            $(".poster-info").append(poster_info_template);
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
            images = splitArray(images);
            images.forEach(image => {
                const template = `<div class="post-image-container"><a href="http://127.0.0.1:81/post_files/${image}" target="_blank"><img class="post-image" src="http://127.0.0.1:81/post_files/${image}"/></a></div>`;
                $("#images-container").append(template);
            });
        } else { // Failed
            
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