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

        <div class="create-post-button-container">
            <p class="title">Questions</p>
            <a class="btn-main" href="/questions/create/">Ask A Question</a>
        </div>

        <div class="posts-container">
            <div class="post">
                <div class="content">
                    <a class="post-title ellipsis" href="#">This is an example post.</a>
                    <p class="text-preview twoline-ellipsis">Here's an example of a post's text content preview.</p>
                </div>
                <div class="meta">
                    <p class="meta-info" id="post-type">Question</p>
                </div>
                <div class="meta">
                    <p class="meta-info" id="views">? views</p>
                    <p class="meta-info" id="upvotes">? upvotes</p>
                    <p class="meta-info" id="downvotes">? downvotes</p>
                    <p class="meta-info" id="answers">? answers</p>
                    <a href="#"><div class="user">
                        <div class="user-image-container"><img class="user-image" src="/users/uploads/profile_images/QuietWind01_user_profile_image_7147288035.png"/></div>
                        <p class="username">username</p>
                        <p class="time">posted on ?</p>
                    </div></a>
                </div>
            </div>
        </div>

    </div>
</body>

<?php require($footer); ?>