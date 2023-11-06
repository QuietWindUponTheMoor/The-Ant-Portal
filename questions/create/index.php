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
            <h1>Ask A Question:</h1>
            <form class="create-post-form" action="" method="POST" enctype="multipart/form-data">
                <div class="textbox">
                    <label for="title">Title</label>
                    <input type="text" id="title">
                </div>
                <div class="textbox">
                    <label for="content">Content</label>                    
                    <textarea name="" id="" cols="30" rows="10"></textarea>
                </div>
                <span class="buttons">
                    <a href="/questions/" class="btn-main btn-cancel">Cancel</a>
                    <button class="btn-main" id="submit" type="submit">Submit</button>
                </span>
                
            </form>
        </div>

    </div>
</body>

<?php require($footer); ?>