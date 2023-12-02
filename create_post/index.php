<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");

if ($isLoggedIn === false) {
    header("Location: /");
}
?>

<body>
    <div class="container">
        <div class="container_sub">
            <?php require($root."/includes/container_left.php"); ?>
            <div class="container_main">



                <form class="form-main" id="create-post-form" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <p class="form-title">Submit A Post</p>
                        <p class="response" id="error"></p>
                        <p class="response" id="warning"></p>
                        <p class="response" id="success"></p>
                    </div>
                    <div class="form-section">
                        <label for="post-type">What are you posting?</label>
                        <select class="input-main" name="postType" id="post-type" required>
                            <option value="1">Question</option>
                            <option value="2">Sighting</option>
                            <option value="3">General</option>
                            <!-- No other options here because changes how stuff is done. -->
                        </select>
                    </div>
                    <div class="form-section">
                        <label for="title">Post title</label>
                        <input class="input-main" type="text" id="title" name="title" minlength="5" maxlength="128" placeholder="Does X species require diapause?" required/>
                    </div>
                    <div class="form-section">
                        <label for="content">Content</label>
                        <textarea class="input-main body-text" id="body" name="body" minlength="5" maxlength="30000" placeholder="If they require diapause, when should I put them in and for how long? What temperature should I keep them at? Should I still feed them?" required></textarea>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="images">Select any relevant images to your post. (Optional)</label>
                        <input class="hidden" type="file" id="images" name="images[]" accept="image/*" multiple/>
                        <button class="btn-action" type="button" id="select-images" onclick="$('#images').click();">Add Images</button>
                    </div>
                    <div class="form-section" id="tags-container">
                        <label class="main-label" for="tags">Enter relevant tags (separated by spaces or commas):</label>
                        <label>Tags cannot contain non-alphabetical [a-Z] characters. Tags are separated by a space, or a comma: ','</label>
                        <!-- Max of 5 tags -->
                        <input class="input-main" type="text" id="tags" name="tagsBefore" placeholder="Example: observation, tetramorium"/>
                        <input class="hidden" type="text" id="hidden-tags" name="final-tags" value="null"/>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="selected-tags">Selected tags. Click to delete.</label>
                        <div class="tags-list" id="selected-tags"></div>
                    </div>
                    <div class="form-section hidden" id="hidden-fields">
                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $userID; ?>" required/>
                        <input type="hidden" id="database" name="database" value="<?php echo $dbHost; ?>" required/>
                    </div>
                    <div class="final-section">
                        <button class="btn-secondary" id="cancel" type="button" onclick="window.history.back();">Cancel Post</button>
                        <button class="btn-warning" id="reset" type="reset">Reset Form</button>
                        <button class="btn-main" id="submit" type="submit">Submit Post</button>
                    </div>
                </form>



            </div>
            <?php require($root."/includes/container_right.php"); ?>
        </div>
    </div>
</body>
<script type="text/javascript" src="/js/lib/post_creation/reg_posts.js"></script>

<?php require($root."/includes/footer.php"); ?>