<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");

if (!isset($_GET["flightID"])) {
    header("Location: /");
}
?>

<body>
    <div class="container">
        <div class="container_sub">
            <?php require($root."/includes/container_left.php"); ?>
            <div class="container_main">


                <div class="post-page-container">
                    <div class="post-section">
                        <p class="post-title">Nuptial Flight #2: Lasius neoniger</p>
                    </div>
                    <div class="post-section section-wrap">
                        <div class="sub-section">
                            <p class="by">Posted by</p>
                            <a class="by by-link" href="/users?userID=1">TestAdmin</a>
                        </div>
                        <div class="sub-section">
                            <p class="by">Edited by</p>
                            <a class="by by-link" href="/users?userID=1">NoUser</a>
                        </div>
                    </div>
                </div>


            </div>
            <?php require($root."/includes/container_right.php"); ?>
        </div>
    </div>
</body>

<?php require($root."/includes/footer.php"); ?>