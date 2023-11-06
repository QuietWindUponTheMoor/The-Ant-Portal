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
            <form class="create-post-form" action="" method="POST" enctype="multipart/form-data">
                <p>Test. Test. Test.</p>
            </form>
        </div>

    </div>
</body>

<?php require($footer); ?>