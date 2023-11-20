<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");

// Initialize Feed class
$posts = new Feed($db);
?>

<body>
    <div class="container">
        <div class="container_sub">


            <?php require($root."/includes/container_left.php"); ?>

            <div class="container_main">
                <div class="container-main-title-container">
                    <p class="title">Recent Activity</p>
                    <a class="btn-main" id="create-post-button" href="/create_post/">Create Post</a>
                </div>
                <div class="container-main-pagination-container">
                    <?php $posts->displayPages(); ?>
                </div>

                <div class="container-main-feed">
                    <?php $posts->feed(); ?>
                </div>
                <div class="container-main-pagination-container">
                    <?php $posts->displayPages(); ?>
                </div>
            </div>

            <?php require($root."/includes/container_right.php"); ?>


        </div>
    </div>
</body>

<?php require($root."/includes/footer.php"); ?>
