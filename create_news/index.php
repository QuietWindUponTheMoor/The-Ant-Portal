<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");
?>

<body>
    <div class="container">
        <div class="container_sub">
            <?php require($root."/includes/container_left.php"); ?>
            <div class="container_main">
                

            
            </div>
            <?php require($root."/includes/container_right.php"); ?>
        </div>
    </div>
</body>

<?php require($root."/includes/footer.php"); ?>