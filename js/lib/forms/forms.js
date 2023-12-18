$("#profile-image-select").on("click", () => {
    $("#profileImage").trigger("click").on("change", function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function (event) {
                $("#profile-image-select").attr("src", event.target.result);
            }

            reader.readAsDataURL(this.files[0])
        }
    });
})