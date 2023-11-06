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
            <p class="title">Ask A Question:</p>
            <form class="create-post-form" action="" method="POST" enctype="multipart/form-data">
                <div class="section">
                    <label for="title">Post title</label>
                    <input class="input-main" type="text" id="title" name="title" minlength="5" maxlength="128" required/>
                </div>
                <div class="section">
                    <label for="content">Content</label>
                    <textarea class="input-main body-text" id="body" name="body" minlength="5" maxlength="30000" required></textarea>
                </div>
                <div class="section">
                    <label for="selected-tags">Selected Tags. Click to delete.</label>
                    <div class="tags-list" id="selected-tags">
                        <p class="tag" onclick="$(this).remove();">Tag 1</p>
                        <p class="tag" onclick="$(this).remove();">Tag 2</p>
                        <p class="tag" onclick="$(this).remove();">Tag 3</p>
                    </div>
                </div>
                <div class="section" id="tags">
                    <label for="tags">Enter relevant tags</label>
                    <input class="input-main" type="text" id="tags" name="tagsBefore"/>
                </div>
                <span class="section buttons">
                    <button class="btn-main btn-cancel" onclick="window.history.back();">Cancel</button>
                    <button class="btn-main" id="submit" type="submit">Submit</button>
                </span>
            </form>
        </div>

    </div>
</body>
<script type="text/javascript" src="/js/lib/post_creation/form_control.js"></script>

<?php require($footer); ?>