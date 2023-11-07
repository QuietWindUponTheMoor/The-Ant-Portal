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

        <div class="create-post-container">
            <p class="title">CREATE A POST:</p>
            <form class="create-post-form" id="create-post-form" action="" method="POST" enctype="multipart/form-data">
                <div class="section">
                    <label for="post-type">What are you posting?</label>
                    <select class="input-main" name="postType" id="post-type" required>
                        <option value="1">Question</option>
                        <option value="2">Sighting</option>
                        <option value="3">Nuptial Flight</option>
                        <!-- Nuptial flight is not an option here, that is a separate page due to needing vastly different formatting. -->
                    </select>
                </div>
                <div class="section">
                    <label for="title">Post title</label>
                    <input class="input-main" type="text" id="title" name="title" minlength="5" maxlength="128" required/>
                </div>
                <div class="section">
                    <label for="content">Content</label>
                    <textarea class="input-main body-text" id="body" name="body" minlength="5" maxlength="30000" required></textarea>
                </div>
                <div class="section">
                    <input class="hidden" type="file" id="images" name="images[]" accept="image/*" multiple/>
                    <button class="btn-secondary" type="button" id="select-images" onclick="$('#images').click();">Add Images</button>
                </div>
                <div class="section">
                    <label for="selected-tags">Selected Tags. Click to delete.</label>
                    <div class="tags-list" id="selected-tags"></div>
                </div>
                <div class="section" id="tags-container">
                    <label for="tags">Enter relevant tags</label>
                    <!-- Max of 5 tags -->
                    <input class="input-main" type="text" id="tags" name="tagsBefore"/>
                    <input class="hidden" type="text" id="hidden-tags" name="final-tags" value="null"/>
                </div>
                <div class="section hidden" id="hidden-fields">
                    <input type="hidden" name="user_id" value="<?php echo $userID; ?>" required/>
                    <input type="hidden" name="database" value="<?php echo $dbHost; ?>" required/>
                </div>
                <span class="section buttons">
                    <button class="btn-main btn-secondary" onclick="window.history.back();">Cancel</button>
                    <button class="btn-main" id="submit" type="submit">Submit</button>
                </span>
            </form>
        </div>

    </div>
</body>
<script type="text/javascript" src="/js/lib/post_creation/form_control.js"></script>

<?php require($footer); ?>