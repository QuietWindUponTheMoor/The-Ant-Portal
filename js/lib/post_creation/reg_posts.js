// Placeholder texts
$questionTitlePH = "Does X species require diapause?";
$questionBodyPH = "If they require diapause, when should I put them in and for how long? What temperature should I keep them at? Should I still feed them?";
$sightingTitlePH = "I noticed X species in Y place today";
$sightingBodyPH = "Found this worker today in <location>, around 3pm.";
$generalTitlePH = "Today I went queen hunting!";
$generalBodyPH = "It was a really nice day during peak nuptial-flight season, so I decided to go queen hunting!";

$("#post-type").on("change", () => {
    let type = parseInt($("#post-type").val());
    const title = $("#title");
    const body = $("#body");

    switch (type) {
        case 1:
            // Question
            title.attr("placeholder", $questionTitlePH);
            body.attr("placeholder", $questionBodyPH);
            break;
        case 2:
            // Sighting
            title.attr("placeholder", $sightingTitlePH);
            body.attr("placeholder", $sightingBodyPH);
            break;
        case 3:
            // General
            title.attr("placeholder", $generalTitlePH);
            body.attr("placeholder", $generalBodyPH);
            break;
        default:
            title.attr("placeholder", $questionTitlePH);
            body.attr("placeholder", $questionBodyPH);
            break;
    }
});

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

    // Initialize final tags
    let final_tags;
    
    if (tags_array.length < 1) {
        final_tags = "NULL";
    } else {
        // Join tags_array
        final_tags = tags_array.join(", ");
    }

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