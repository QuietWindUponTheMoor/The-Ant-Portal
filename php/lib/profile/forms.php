<?php

// If user selects a profile image
if (isset($_POST["type"])) {
    // Shorten type variable
    $type = $_POST["type"];

    // Check for different types
    if ($type === "profile-image") {
        // Upload the new image
        $upload = new FileUpload("image", "/users/uploads/profile_images", $username."_user_profile_image");
        // Upload image & get new path
        $newImagePath = $upload->uploadSingleFile();
        // Update database to reflect new profile image path
        $sql = "UPDATE users SET image=? WHERE userID=?;";
        $stmt = mysqli_stmt_init($db->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_bind_param($stmt, "si", $newImagePath, $userID)
            or die("Could not bind SQL parameters.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
    } else if ($type === "banner-image") {
        // Upload the new image
        $upload = new FileUpload("banner", "/users/uploads/banner_images", $username."_user_banner_image");
        // Upload image & get new path
        $newImagePath = $upload->uploadSingleFile();
        // Update database to reflect new profile image path
        $sql = "UPDATE users SET banner=? WHERE userID=?;";
        $stmt = mysqli_stmt_init($db->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_bind_param($stmt, "si", $newImagePath, $userID)
            or die("Could not bind SQL parameters.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
    } else if ($type === "change-username") {
        // If user changes their username
        // Update database to reflect new username
        $sql = "UPDATE users SET username=? WHERE userID=?;";
        $stmt = mysqli_stmt_init($db->conn)
            or die("Could not initiate a connection.");
        mysqli_stmt_prepare($stmt, $sql)
            or die("Could not prepare SQL statement.");
        mysqli_stmt_bind_param($stmt, "si", $_POST["username"], $userID)
            or die("Could not bind SQL parameters.");
        mysqli_stmt_execute($stmt)
            or die("Could not execute SQL sequence.");
        mysqli_stmt_close($stmt)
            or die("Could not close SQL connection.");
    } else if ($type === "delete-profile") {
        // User chooses to delete profile
        // Warning, sensitive actions ahead:
        $userIDToDelete = $_POST["userID"];
        if ($db->transferTableData("users_archive", "users", "userID=$userIDToDelete")) {
            // If successfully deleted (soft-delete) profile/account
        }
    }
}