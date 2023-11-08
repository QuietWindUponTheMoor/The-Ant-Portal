<?php
$head = $_SERVER["DOCUMENT_ROOT"];
$head .= "/includes/head.php";
$nav = $_SERVER["DOCUMENT_ROOT"];
$nav .= "/includes/navbar.php";
$footer = $_SERVER["DOCUMENT_ROOT"];
$footer .= "/includes/footer.php";
require($head);
require($nav);

if (isset($_GET["logout"]) && $_GET["logout"] == true) {
    $_SESSION["userID"] = null;
    session_destroy();
    header("Location: /users/login/");
}
?>

<body>
    <div class="container">

        <div class="create-post-container">
            <div class="create-post-button-container">
                <p class="title">Are you sure you want to log out?</p>
            </div>
            <p id="logout-response"></p>
            <div class="section buttons">
                <button class="btn-secondary" onclick="window.history.back();" type="button">Cancel</button>
                <a class="btn-main" id="confirm" type="button">Logout</a>
            </div>
        </div>

    </div>
</body>
<script type="text/javascript">
$("#confirm").on("click", () => {
    $("#logout-response").text("Logging you out, please wait.");
    setTimeout(() => {
        $("#logout-response").text("Logging you out, please wait..");
        setTimeout(() => {
            $("#logout-response").text("Logging you out, please wait...");
            setTimeout(() => {
                $("#logout-response").text("Logging you out, please wait....");
                setTimeout(() => {
                    $("#logout-response").text("Logging you out, please wait.....");
                    window.location.assign("?logout=true");
                }, 1000);
            }, 1000);
        }, 1000);
    }, 1000);
});
</script>

<?php require($footer); ?>
