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

$("#registration-form").on("submit", function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    // Manage profile image
    const profileImage = document.getElementById("profileImage");
    if (profileImage.files.length > 0) {
        formData.append("profileImage", profileImage.files[0]);
    }
    // Append the time and date
    formData.append("joined", new Date().getTime());

    $.ajax({  
        type: "POST",  
        url: "http://127.0.0.1:81/" + encodeURIComponent("register"), 
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            // Process response
            const data = response;
            const status = data.status;
            const message = data.message;
            const details = data.message;
            const profile_image = data.profile_image_name;
            if (status === 200) { // OK
                $("#heading").text(message).css("color", "limegreen");
                $("#subheading").text("Please wait a moment...");
                setTimeout(() => {
                    window.location.assign("/users/0/signin");
                }, 3000);
            }
        }
    });
});