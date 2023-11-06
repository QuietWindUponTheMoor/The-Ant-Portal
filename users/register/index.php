<?php
$head = $_SERVER["DOCUMENT_ROOT"];
$head .= "/includes/head.php";
$nav = $_SERVER["DOCUMENT_ROOT"];
$nav .= "/includes/navbar.php";
$footer = $_SERVER["DOCUMENT_ROOT"];
$footer .= "/includes/footer.php";
require($head);
require($nav);

// Get time & date
$date = date("M d, Y");
$timezone = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
$timezone = $timezone->format('T');
$time = date("h:ia");
$time = "$time ($timezone)";
?>

<body>
    <div class="container">

        <form class="registration-form" action="register.php" method="POST" enctype="multipart/form-data">
            <div class="section">
                <p class="registration-meta">Sign up for an account with us! (P.S., You get +5 seeds just for creating an account!)</p>
                <?php
                // If POST data has been sent/form has been submitted
                if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["pass"]) && isset($_POST["passRpt"])) {
                    // Register new user
                    new Register($_POST["username"], $_POST["email"], $_POST["pass"], $_POST["passRpt"], $date, $time);
                }
                ?>
            </div>
            <div class="section">
                <label for="username">Your desired username</label>
                <input class="text-input" type="text" name="username" id="username" minlength="3" maxlength="24" required/>
            </div>
            <div class="section">
                <label for="email">Your email address</label>
                <input class="text-input" type="email" name="email" id="email" minlength="3" maxlength="128" required/>
            </div>
            <div class="section">
                <label for="pass">Your desired password</label>
                <input class="text-input" type="password" name="pass" id="pass" minlength="8" maxlength="128" required/>
            </div>
            <div class="section">
                <label for="passRpt">Re-enter your password</label>
                <input class="text-input" type="password" name="passRpt" id="passRpt" minlength="8" maxlength="128" required/>
            </div>
            <div class="section">
                <div class="preview-container">
                    <img class="image-preview" id="image-preview"/>
                </div>
            </div>
            <div class="section image-select">
                <label for="image">Select a profile image (optional):</label>
                <input style="display: none;" type="file" name="image" id="imageHidden" accept="image/*"/>
                <button class="btn-main" type="button" id="selectImage" onclick="$('#imageHidden').click();">Select Image</button>
            </div>
            <div class="section final-section">
                <button class="btn-main" id="submit" type="submit">Sign Up!</button>
            </div>
        </form>

        <script type="text/javascript" src="js/lib/functions/functions.js">
        // If user selects an image
        previewImageBeforeUpload("#imageHidden", "#preview-container", $(".registration-form").width(), "#image-preview", "flex");
        </script>

    </div>
</body>

<?php require($footer); ?>