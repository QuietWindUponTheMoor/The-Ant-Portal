function previewImageBeforeUpload(OnChangeEl, previewContainer, previewContainerMaxWidthChange, imagePreviewEl, imagePreviewElDisplayAfter) {
    $(OnChangeEl).on("change", async (event) => {
        // If files array isn't empty
        if (event.target.files.length > 0) {
            // Set max width of preview
            $(previewContainer).css("max-width", previewContainerMaxWidthChange);
            // Create URL object for image preview
            let src = URL.createObjectURL(event.target.files[0]);
            // Set preview element
            $(imagePreviewEl).attr("src", src);
            // Display the image
            $(imagePreviewEl).css("display", imagePreviewElDisplayAfter);
        }
    });
}

