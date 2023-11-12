

<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");
?>

<body>
    <div class="container">
        <div class="container_sub">
            <?php require($root."/includes/container_left.php"); ?>
            <div class="container_main">



                <form class="form-main" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <p class="form-title">Submit A Nuptial Flight Observation</p>
                    </div>
                    <div class="form-section">
                        <label for="species">What species did you observe? Make sure the species is spelled/formatted correctly.</label>
                        <label>The first word (genus) should be capitalized, followed by a space; the second word (species) should be all-lowercased.</label>
                        <input class="input-main" type="text" id="species" name="species" minlength="5" maxlength="256" placeholder="Example: Lasius neoniger" required/>
                    </div>
                    <div class="form-section">
                        <label for="body">Describe this flight and any relevant details.</label>
                        <textarea class="input-main" id="body" name="body" minlength="5" maxlength="30000" placeholder="Example: It was sunny and about 78 degrees fahrenheit (F) outside with little wind." required></textarea>
                    </div>
                    <div class="form-section">
                        <label for="date">Enter the date of the observation.</label>
                        <label>Please use the formatt YYYY-DD-MM, like shown below:</label>
                        <input class="input-main" type="text" id="date" name="date" minlength="10" maxlength="10" placeholder="Example: 2023-16-06" required/>
                    </div>
                    <div class="form-section">
                        <label for="time">Enter the time of the observation.</label>
                        <label>Please use the formatt HH:MMam/pm, like shown below:</label>
                        <input class="input-main" type="text" id="time" name="time" minlength="10" maxlength="10" placeholder="Example: 06:43pm" required/>
                    </div>
                    <div class="form-section">
                        <label for="time">Enter the outdoor temperature at the time of the observation.</label>
                        <label>You can use C or F for either celcius or fahrenheit. Please format like shown below (this recording will be translated to celcius or fahrenheit):</label>
                        <input class="input-main" type="text" id="time" name="time" minlength="10" maxlength="10" placeholder="Example: 76F" required/>
                    </div>
                    <div class="form-section">
                        <label for="time">Please enter the wind speed during the observation.</label>
                        <label>Enter the wind speed as just a number in mph (miles-per-hour). If there was no wind during the observation, please enter "0". Or, if you'd like to opt out, please enter "n/a".</label>
                        <label>You can use a conversion tool below:</label>
                        <label><a class="label-link" href="https://www.unitconverters.net/speed/mph-to-kph.htm" target="_blank">mph -> kmph conversion tool</a></label>
                        <input class="input-main" type="text" id="wind-speed" name="wind-speed" minlength="1" maxlength="2" placeholder="Example: 76F" required/>
                    </div>
                    <div class="form-section">
                        <label for="time">Enter the moon cycle at the time of the observation.</label>
                        <label>Please use the format of "waning-gibbous" and not "waning gibbous" as examples.</label>
                        <input class="input-main" type="text" id="moon-cycle" name="moon-cycle" minlength="5" maxlength="50" placeholder="Example: waning-gibbous" required/>
                    </div>


                    <div class="form-section" id="tags-container">
                        <label for="tags">Enter relevant tags (separated by spaces or commas):</label>
                        <label>Tags cannot contain non-alphabetical [a-Z] characters.</label>
                        <!-- Max of 5 tags -->
                        <input class="input-main" type="text" id="tags" name="tagsBefore" placeholder="Example: observation, tetramorium"/>
                        <input class="hidden" type="text" id="hidden-tags" name="final-tags" value="null"/>
                    </div>
                    <div class="form-section">
                        <label for="selected-tags">Selected tags. Click to delete.</label>
                        <div class="tags-list" id="selected-tags"></div>
                    </div>

                    
                    <div class="final-section">
                        <button class="btn-secondary" id="cancel" type="button">Cancel Post</button>
                        <button class="btn-warning" id="reset" type="reset">Reset Form</button>
                        <button class="btn-main" id="submit" type="submit">Submit Nuptial Flight</button>
                    </div>
                </form>



            </div>
            <?php require($root."/includes/container_right.php"); ?>
        </div>
    </div>
</body>
<script type="text/javascript">
function getCurrentDate() {
    let currentDate = new Date();
    let formattedDate = currentDate.getFullYear() + '-' + ('0' + (currentDate.getDate())).slice(-2) + '-' + ('0' + (currentDate.getMonth() + 1)).slice(-2);
    return formattedDate;
}
function getMinDate() {
    let currentDate = new Date();
    let fiveYearsAgo = new Date(currentDate.getFullYear() - 5, currentDate.getMonth(), currentDate.getDate());
    let formattedDate = fiveYearsAgo.getFullYear() + '-' + ('0' + (fiveYearsAgo.getDate())).slice(-2) + '-' + ('0' + (fiveYearsAgo.getMonth() + 1)).slice(-2);
    return formattedDate;
}


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
$(".form-main").submit(async (event) => {
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
        console.log(response)
        if (response === -1) {
            console.error("Something went wrong with redirecting you.");
        } else {
            window.location.assign("/posts?postID=" + response);
        }
    });
});



// Helper functions
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

</script>

<?php require($root."/includes/footer.php"); ?>
