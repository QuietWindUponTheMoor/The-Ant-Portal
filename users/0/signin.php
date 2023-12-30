<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");
?>

<body>
    <div class="container">
    
        <form class="form-main col main-object" id="signin-form" action="" method="POST" enctype="multipart/form-data">
            <div class="section col">
                <p class="heading" id="heading">Sign in to your account</p>
                <p class="subheading" id="subheading">
                    <?php
                    if (
                        isset($_GET["set_session"]) &&
                        isset($_GET["user_id"]) &&
                        isset($_GET["username"]) &&
                        isset($_GET["email"]) &&
                        isset($_GET["profile_image"])
                        ) {
                        // Set the session but first set session timeout
                        $lifetime = 60 * 60 * 12; // 12 hours

                        // Set the session data
                        $_SESSION["isLoggedIn"] = true;
                        $_SESSION["user_id"] = $_GET["user_id"];
                        $_SESSION["username"] = $_GET["username"];
                        $_SESSION["email"] = $_GET["email"];
                        $_SESSION["profile_image"] = $_GET["profile_image"];

                        // If all else is good, redirect the user to the main page.
                        header("Location: /");
                    }
                    ?>
                </p>
            </div>
            <div class="section col">
                <label for="user">Username Or Email</label>
                <input type="text" class="input-main" id="login" name="login" required/>
            </div>
            <div class="section col">
                <label for="pass">Password</label>
                <input type="password" class="input-main" id="pass" name="pass" required/>
            </div>
            <div class="section col">
                <button class="btn-main" type="submit">Sign In</button>
            </div>
        </form>
        <script type="text/javascript" src="/js/lib/forms/forms.js"></script>

    </div>
</body>

<?php require($root."/includes/footer.php"); ?>
