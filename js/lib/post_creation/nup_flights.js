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
$("#nup-flight-form").submit(async (event) => {
    // Prevent default actions
    event.preventDefault();
    // Refresh errors/warnings/etc
    $(".response").text("");
    // Join tags_array
    let final_tags = tags_array.join(", ");
    // Insert into #hidden-tags input
    $("#hidden-tags").val(final_tags);

    await validateTime($("#time").val())

    // Validate inputs
    let tempValid = await validateTemp($("#temperature").val());
    let moonCycleValid = await validateMoonCycle($("#moon-cycle").val());
    let windSpeedValid = await validateWindSpeed($("#wind-speed").val());
    let timeValid = await validateTime($("#time").val());
    if (tempValid === false || moonCycleValid === false || windSpeedValid === false || timeValid === false) {
        return;
    }

    // Append formatted changes to form
    $("#date").val(await convertDateFormat($("#date").val()));
    $("#moon-cycle").val(await convertMoonCycleFormat($("#moon-cycle").val()));
    $("#temperature").val(await convertTemperatureFormat($("#temperature").val()));
    $("#time").val(await convertTime($("#time").val()));
    
    // Get append values:
    const body = getBodyValue();

    // Create formData
    const formData = new FormData(document.getElementById("nup-flight-form"));
    formData.append("data-body", body);

    // Execute AJAX
    await sendAJAX("/php/lib/posts/create_nup_flight.php", formData, "POST", false, false, function (response) {
        console.log(response) // TESTING, TEMPORARY
        if (response === -1) {
            console.error("Something went wrong with redirecting you.");
        } else {
            window.location.assign("/nup_flights?flightID=" + response);
        }
    });
});



// Helper functions
async function validateTime(input) {
    // Convert the time
    let checkedInput = await convertTime(input);

    console.log(checkedInput);

    // Check that format is correct
    const isValidFormat = /^(0?[1-9]|1[0-2]):([0-5][0-9])(am|pm)$/i.test(checkedInput);

    if (!isValidFormat) {
        $("#error").text(`The time "${input}" is not valid. The correct format should be "HH:MM<am/pm>".`);
        return false;
    }

    // If all else is good, return true
    return true;
}
async function validateDate(input) {
    // Replace "/" with "-" if user didn't follow directions
    let properFormat = input.replace(/\//g, '-');
    let parts = properFormat.split('-');

    // Check parts
    if (parts.length !== 3) {
        $("#error").text(`The format "${properFormat}" is not valid. The format must be "YYYY-DD-MM".`);
        return false;
    }

    // Destructure parts
    const [year, day, month] = parts;

    // Convert to integer
    const yearInt = parseInt(year, 10);
    const dayInt = parseInt(day, 10);
    const monthInt = parseInt(month, 10);

    // Validate the day and month
    if (dayInt > 31) {
        $("#error").text(`The day ("DD") must be 31 or lower. You gave "${dayInt}".`);
        return false;
    } else if (monthInt > 12) {
        $("#error").text(`The month ("MM") must be 12 or lower. You gave "${dayInt}".`);
        return false;
    }

    // Format date as "YYYY-DD-MM"
    const formattedDate = `${year}-${day.padStart(2, '0')}-${month.padStart(2, '0')}`;

    // Get current date and minimum date
    const currentDate = await getCurrentDate();
    const minDate = await getMinDate();

    // Validate date
    if (formattedDate < minDate) {
        $("#error").text(`The input date cannot be earlier than ${minDate}.`);
        return false;
    } else if (formattedDate > currentDate) {
        $("#error").text(`The input date cannot be later than today's date (${currentDate}).`);
        return false;
    }

    // If all else is good, return true
    return true;
}
async function validateWindSpeed(input) {
    // Convert to integer, just in case
    let speed = parseInt(input);

    // Check if speed is within valid range
    if (speed < 0) {
        $("#error").text(`The wind speed cannot be lower than 0mph.`);
        return false;
    } else if (speed > 130) {
        $("#error").text(`The wind speed cannot be higher than 0mph.`);
        return false;
    }

    // If all else is good, return true
    return true;
}
async function validateMoonCycle(input) {
    // Format the input
    let formattedCycle = input.toLowerCase().replace(/\s+/g, "-");

    // Get list of valid cycles
    const validCycles = ["full-moon", "new-moon", "first-quarter", "waning-crescent", "waning-gibbous", "waxing-gibbous", "third-quarter", "waxing-crescent"];

    // Validate whether the formatted input is valid
    if (!validCycles.includes(formattedCycle)) {
        $("#error").text(`The moon cycle you entered is not valid. The valid inputs are: ${validCycles.join(", ")}`);
        return false;
    }

    // If all else is good, return true
    return true;
}
async function validateTemp(input) {
    // Convert to lowercase
    let newTemperature = input.toLowerCase();
    // Do regex
    let regEx = /^(-?\d{1,2})([cf])$/;
    let validFormat = newTemperature.match(regEx);

    // Check that the format is valid
    if (!validFormat) {
        $("#error").text("The format of the temperature you entered is not valid. It must be in the format of: ##F or ##C, with # being a digit.");
        return false;
    }

    // Destructure format
    const [, temperature, unit] = validFormat;

    // Convert C to F only if the temperature entered was in Celcius
    const fahrenheit = unit === 'c' ? (parseInt(temperature, 10) * 9) / 5 + 32 : parseInt(temperature, 10);

    // Check that temperature (in fahrenheit) is in a valid range
    if (fahrenheit < -25 || fahrenheit > 130) {
        $("#error").text(`The temperature entered must be between -25F and 130F. The temperature you entered was ${fahrenheit}.`);
        return false;
    }

    // If all else is good, return true
    return true;
}
async function convertTime(input) {
    // Convert input to lowercase
    const lowercasedInput = input.toLowerCase();
    // Remove any spaces in the input
    let time = lowercasedInput.replace(/\s/g, "");

    // Check if the hour is between 1 and 9. If it's a single digit, add a leading zero.
    const [hours, minutesAndAmPm] = time.split(':');
    const formattedHours = (hours.length === 1) ? `0${hours}` : hours;

    // Make sure the "am" or "pm" part has the "m" at the end of it.
    const amPm = (minutesAndAmPm.includes('am') || minutesAndAmPm.includes('pm')) ? minutesAndAmPm : `${minutesAndAmPm}m`;

    // Return converted format
    return `${formattedHours}:${amPm}`;
}
async function  convertTemperatureFormat(temp) {
    // Convert to lowercase
    let newTemperature = temp.toLowerCase();
    // Do regex
    let regEx = /^(-?\d{1,2})([cf])$/;
    let validFormat = newTemperature.match(regEx);
    // Destructure format
    const [, temperature, unit] = validFormat;
    // Convert C to F only if the temperature entered was in Celcius
    const fahrenheit = unit === 'c' ? (parseInt(temperature, 10) * 9) / 5 + 32 : parseInt(temperature, 10);
    // Return
    return fahrenheit;
}
async function convertMoonCycleFormat(cycle) {
    return cycle.toLowerCase().replace(/\s+/g, "-");
}
async function convertDateFormat(date) {
    return date.replace(/\//g, '-');
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
async function getCurrentDate() {
    let currentDate = new Date();
    let formattedDate = currentDate.getFullYear() + '-' + ('0' + (currentDate.getDate())).slice(-2) + '-' + ('0' + (currentDate.getMonth() + 1)).slice(-2);
    return formattedDate;
}
async function getMinDate() {
    let currentDate = new Date();
    let fiveYearsAgo = new Date(currentDate.getFullYear() - 5, currentDate.getMonth(), currentDate.getDate());
    let formattedDate = fiveYearsAgo.getFullYear() + '-' + ('0' + (fiveYearsAgo.getDate())).slice(-2) + '-' + ('0' + (fiveYearsAgo.getMonth() + 1)).slice(-2);
    return formattedDate;
}