// Manage tags
// Initialize tags_array
let tags_array = [];
$("#tags").on("keyup", (event) => {
    // If user presses space or comma (,) to add the tag
    if (event.key === "," || event.key === " ") {
        // Check if tag limit is reached
        if (!tagLimitCheck(tags_array)) {
            alert("You have reached the max amount of tags (5).");
            return;
        }
        // Get current text in input
        let input = $("#tags").val().replace(/[^a-zA-Z]/g, "");
        // Append tag
        appendTag(input);
        // Remove text in input box
        $("#tags").val("");
        // Now add to array
        tags_array.push(input);
    }
});

// Form submit
$("#create-post-form").submit(async (event) => {
    // Prevent default actions
    event.preventDefault();
    // Join tags_array
    let final_tags = tags_array.join(", ");
    // Insert into #hidden-tags input
    $("#hidden-tags").val(final_tags);
    
    // Get append values:
    const body = getBodyValue();

    // Create formData
    const formData = new FormData(document.getElementById("create-post-form"));
    formData.append("data-body", body);

    // Execute AJAX
    await sendAJAX("/php/lib/posts/create.php", formData, "POST", false, false, function (response) {
        if (response === -1) {
            console.error("Something went wrong with redirecting you.");
        } else if (response > 0) {
            window.location.assign("/posts?postID=" + response);
        } else {
            console.log(response);
        }
    });
});


// Helper functions
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
function removeTag(element) {
    // Remove/un-append tag
    element.remove();
    // Fetch tags array
    let new_tags = tags_array;
    // Get index of item value
    let value = element.text();
    new_tags = new_tags.filter((e) => {
        return e !== value;
    });
    // Set tags_array
    tags_array = new_tags;
    // Change value of #hidden-tags
    $("#hidden-tags").val(new_tags.join(", "));
}
function appendTag(value) {
    // Generate element string:
    let elString = `<p class="tag" onclick="removeTag($(this));">${value}</p>`;
    // Append tag:
    $("#selected-tags").append(elString);
}
function tagLimitCheck(array) {
    if (countArrayItems(array) >= 5) {
        return false;
    } else {
        return true;
    }
}
function countArrayItems(array) {
    return array.length;
}
function getBodyValue() {
    return $("#body").val().replace(/\n/g, "<br>");
}