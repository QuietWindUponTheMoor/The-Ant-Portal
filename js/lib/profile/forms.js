// If user is logged in, owns the profile, AND clicks on image:
$(".main-image-on-hover").on("click", () => {
    // Click the hidden input element
    $("#image").click();
});

// If user selects a new profile image
$("#image").on("change", () => {
    // Submit the form
    $("#change-profile-image-form").submit();
});
// If user selects a new banner image
$("#banner").on("change", () => {
    // Submit the form
    $("#change-profile-banner-form").submit();
});
// If user chooses to delete profile
$("#confirm-delete-profile").on("click", () => {
    // Submit the form
    $("#delete-profile-form").submit();
});