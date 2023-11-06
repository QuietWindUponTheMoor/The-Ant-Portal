// If user is logged in, owns the profile, AND clicks on image:
$(".main-image-on-hover").on("click", () => {
    // Click the hidden input element
    $("#image").click();
});

// If user selects an image
$("#image").on("change", () => {
    // Submit the form
    $("#change-profile-image-form").submit();
});