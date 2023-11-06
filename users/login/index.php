<?php
$head = $_SERVER["DOCUMENT_ROOT"];
$head .= "/includes/head.php";
$nav = $_SERVER["DOCUMENT_ROOT"];
$nav .= "/includes/navbar.php";
$footer = $_SERVER["DOCUMENT_ROOT"];
$footer .= "/includes/footer.php";
require($head);
require($nav);
?>

<body>
    <div class="container">

        <form class="registration-form" action="login.php" method="POST" enctype="multipart/form-data">
            <div class="section">
                <p class="registration-meta">Sign in to your account!</p>
                <?php
                // If POST data has been sent/form has been submitted
                if (isset($_POST["username"]) && isset($_POST["pass"])) {
                    // Register new user
                    new Login($_POST["username"], $_POST["pass"]);
                }
                ?>
            </div>
            <div class="section">
                <label for="username">Your username</label>
                <input class="text-input" type="text" name="username" id="username" minlength="3" maxlength="24" required/>
            </div>
            <div class="section">
                <label for="pass">Your password</label>
                <input class="text-input" type="password" name="pass" id="pass" minlength="8" maxlength="128" required/>
            </div>
            <div class="section final-section">
                <button class="btn-main" id="submit" type="submit">Sign in!</button>
            </div>
        </form>

    </div>
</body>

<?php require($footer); ?>