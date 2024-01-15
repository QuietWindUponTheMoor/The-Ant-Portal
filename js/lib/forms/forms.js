// Registration
$("#profile-image-select").on("click", () => {
    $("#profileImage").trigger("click").on("change", function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function (event) {
                $("#profile-image-select").attr("src", event.target.result);
            }

            reader.readAsDataURL(this.files[0]);
        }
    });
});
$("#registration-form").on("submit", function(event) { // Register submit
    event.preventDefault();

    // Get form data
    const formData = new FormData(this);

    // Manage profile image
    const profileImage = document.getElementById("profileImage");
    if (profileImage.files.length > 0) {
        formData.append("profileImage", profileImage.files[0]);
    }
    // Append the time and date
    formData.append("joined", new Date().getTime());

    // Submit to API
    $.ajax({  
        type: "POST",  
        url: API_addr + encodeURIComponent("register"), 
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            // Process response
            const data = response;
            const status = data.status;
            const message = data.message;
            const details = data.details;
            const profile_image = data.profile_image_name; // For use later
            if (status === 200) { // OK
                $("#heading").text(message).css("color", "limegreen");
                $("#subheading").text("Please wait a moment...");
                setTimeout(() => {
                    window.location.assign("/users/0/signin");
                }, 3000);
            } else {
                $("#heading").text("Registration failed").css("color", "brightred");
                $("#subheading").text(details);
            }
        }
    });
});

// Sign in
$("#signin-form").on("submit", function(event) { // Sign in submit
    event.preventDefault();

    // Get form data
    const formData = new FormData(this);

    // Submit to API
    $.ajax({  
        type: "POST",  
        url: API_addr + encodeURIComponent("signin"), 
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            // Process response
            const data = response;
            const status = data.status;
            const message = data.message;
            const details = data.details;
            const user_data = data.user_data;
            if (status === 200) { // OK
                $("#heading").text(message).css("color", "limegreen");
                $("#subheading").text("Please wait a moment...");
                setTimeout(() => {
                    window.location.assign(`/users/0/session_ini_set?set_session=true&user_id=${user_data.user_id}&username=${user_data.username}&email=${user_data.email}&profile_image=${user_data.image}`);
                }, 3000);
            } else {
                $("#heading").text(message).css("color", "brightred");
                $("#subheading").text(details);
            }
        }
    });
});