<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");


if ($isLoggedIn == 0) {
    header("/redirects/not_signed_in.php");
}

if (/*!isset($_GET["post_type"]) && */ !isset($_GET["post_id"])) {
    header("/redirects/404.php");
}
?>
<!--
Types:
0 = Question
1 = General
2 = informative
3 = observation
4 = nuptial_flight
-->
<script type="text/javascript">
const post_id = "<?php echo $_GET["post_id"] ?? null; ?>";
const thisUserID = "<?php echo $_SESSION["user_id"] ?? null; ?>";
let login_status = 400;
if (isLoggedIn == 1) {
    login_status = 200;
    
} else {
    window.location.assign("/redirects/404");
}
console.log(`Login status ${login_status}`);
</script>
<body>
    <div class="container" id="post-page-container">
    
        <!-- ORIGINAL POST SECTION -->
        <div class="main-object row post-object">

            <div class="content col">
                <p class="content-header" id="post-type">Question</p>

                <div class="post-actions row">
                    <div class="action-container col" id="voting">
                        <p class="action-title">Vote for this post</p>
                        <div class="action-contents">
                            <div class="vote-image-container"><img class="vote-image" id="upvote" src="/web_images/icons/upvote.png"/></div>
                            <p class="vote-count" id="vote-count">--</p>
                            <div class="vote-image-container"><img class="vote-image" id="downvote" src="/web_images/icons/downvote.png"/></div>
                        </div>
                    </div>
                    <div class="action-container col" id="research-details">
                        <p class="action-title">Research-Specific Details</p>
                        <div class="data-container row">
                            <div class="research-item row" id="temperature">
                                <p class="item-data" id="label">Temperature:</p>
                                <p class="item-data" id="data">--</p>
                            </div>
                            <div class="research-item row" id="wind-speed">
                                <p class="item-data" id="label">Wind Speed:</p>
                                <p class="item-data" id="data">--</p>
                            </div>
                            <div class="research-item row" id="moon-cycle">
                                <p class="item-data" id="label">Moon Cycle:</p>
                                <p class="item-data" id="data">--</p>
                            </div>
                            <div class="research-item row" id="latitude">
                                <p class="item-data" id="label">Latitude:</p>
                                <p class="item-data" id="data">--</p>
                            </div>
                            <div class="research-item row" id="longitude">
                                <p class="item-data" id="label">Longitude:</p>
                                <p class="item-data" id="data">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="action-container col" id="tags-list">
                        <p class="action-title">Tags for this post</p>
                        <div class="action-contents" id="tags"></div>
                    </div>
                </div>

                <div class="body-content col">
                    <p class="post-text" id="title">--</p>
                <p id="post-time">--</p>
                    <p class="post-text" id="body">--</p>
                </div>

                <div class="post-images" id="images-container"></div>

                <div class="post-actions row poster-info has-edit-info">
                    <div class="views-and-answers">
                        <p class="views-and-answers-count" id="view-count">--</p>
                        <p class="views-and-answers-count" id="answer-count">--</p>
                    </div>
                    <!-- If the post has edit info, edit data will display here as well -->
                </div>

                <!-- Create answer button -->
                <button class="btn-white" type="button" id="open-answer-form">Answer This Question</button>

            </div>



            <div class="comments col" id="comments-container">
                <p class="comments-header">User Replies</p>
                <div class="replies-list col" id="main-replies-list"></div>
                <div class="textbox-container col">
                    <label for="reply-textbox">Post a comment</label>
                    <div class="textbox-content row">
                        <input type="text" class="input-main" id="reply-textbox"/>
                        <button class="btn-white" type="button" id="create-reply">Reply</button>
                    </div>
                </div>
            </div>

        </div>
        <!-- CREATE ANSWER FORM SHALL BE APPENDED HERE -->
    </div>
</body>
<script type="text/javascript" src="/js/lib/posts/create.js"></script>
<script type="text/javascript" src="/js/lib/posts/type_select.js"></script>
<script type="text/javascript" src="/js/lib/posts/fetch_data.js"></script>
<?php echo $jsImports["functions"]; ?>
<script type="text/javascript" src="/js/lib/posts/post_and_answer_voting.js"></script>
<script type="text/javascript" src="/js/lib/posts/create_reply.js"></script>

<?php require($root."/includes/footer.php"); ?>