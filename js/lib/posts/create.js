/*
Types:
0 = Question
1 = General
2 = informative
3 = observation
4 = nuptial_flight
*/

// Submitting the form
$("#post-creation-form").on("submit", function(event) {
    // Prevent default
    event.preventDefault();

    // Fetch append data
    // Get the post type
    const post_type = parseInt($(".type-select").val());
    // Get tags list
    const all_tags = tags;
    // Get images
    const all_images = images_obj;

    // Initialize form data class
    const form_data = new FormData();

    // Append items to form_data
    const form = $("#post-creation-form")[0];
    $(form.elements).each(function() {
        const input = $(this);
        // Iterate over inputs and exclude certain names
        if (input.attr("name") !== "type" && input.attr("name") !== "tags-input" && input.attr("name") !== undefined) {
            // If it's not a file
            if (input.attr("type") !== "file") {
                form_data.append(input.attr("name"), input.val());
            }
        }
    });

    // Append manual items
    form_data.append("post_type", post_type);
    form_data.append("all_tags", all_tags);
    all_images.forEach((image, index) => {
        form_data.append(`image_${index}`, image.file, image.name);
    });
    // Append the time and date
    form_data.append("joined", new Date().getTime());

    // Submit to API
    $.ajax({  
        type: "POST",  
        url: "http://127.0.0.1:81/" + encodeURIComponent("post_create"), 
        data: form_data,
        processData: false,
        contentType: false,
        success: function (response) {
            // Process response
            const data = response;
            const status = data.status;
            const message = data.message;
            const details = data.details;
            const post_id = data.post_id;
            if (status === 200) { // OK
                $("#heading").text(message).css("color", "limegreen");
                $("#subheading").text("Please wait a moment...");
                setTimeout(() => {
                    window.location.assign(`/post?id=${post_id}`);
                }, 3000);
            } else {
                $("#heading").text(message).css("color", "brightred");
                $("#subheading").text(details);
            }
        }
    });
});

// Appending tags
$("#fields-list").on("keyup", "#tags-input", function(event) {
    if (event.key === ",") {
        // Grab the value of the tag
        const tag_value = fetchStrippedTagValue($(this).val());

        // Push to array & clear textbox
        tags.push(tag_value);
        $(this).val("");

        // Append the tag
        $("#selected-tags").append(`<div class="tag">${tag_value}</div>`);
    }
});

// Removing tags
$("#fields-list").on("click", ".tag", function() {
    // Get the value of the tag
    const stripped_tag = fetchStrippedTagValue($(this).text());

    // Remove the tag
    $(this).remove();

    // Remove the tag from the list
    tags = removeItemFromArray(tags, stripped_tag);
});

// Handle images
$("#fields-list").on("change", "#image", function(event) {
    // Get select image collection
    const images = event.target.files;

    // Iterate over all selected files
    for (let i = 0; i < images.length; i++) {
        // Create temporary URL for the image
        const image = images[i];
        const image_url = URL.createObjectURL(image);

        // Append the images to the preview object
        const template =
        `
        <div class="image-preview-container row" id="image-${i}" image-index="${i}">
            <a href="${image_url}" target="_blank" title="Click to enlarge image preview"><img class="image-preview" src="${image_url}"/></a>
            <p class="image-name">${image.name}</p>
        </div>
        `;
        $("#selected-images").append(template);

        // Append to image_obj
        images_obj.push({
            name: image.name,
            url: image_url,
            file: image
        });
    }
});

// Removing images
$("#fields-list").on("contextmenu", ".image-preview-container", function(event) {
    // Prevent contextmenu from appearing
    event.preventDefault();

    // Get the preview's ID
    const preview_index = parseInt($(this).attr("image-index"), 10);

    // Remove image from object
    images_obj = removeItemFromObj(images_obj, preview_index);

    // Remove from page/unappend
    $(this).remove();
});




// Functions
function fetchStrippedTagValue($item_value) {
    return $item_value.toLowerCase().replace(/[^a-zA-Z0-9-]/g, ""); // Strips everything except alphanumeric characters and dashes
}
function removeItemFromArray(array, search) {
    return array.filter(item => item !== search);
}
function removeItemFromObj(object, index) {
    // Remove item from object
    object.splice(index, 1);

    // Return
    return object;
}