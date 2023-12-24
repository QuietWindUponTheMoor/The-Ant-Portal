<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");
?>

<body>
    <div class="container">
    
        <form class="form-main col main-object" id="signin-form" action="" method="POST" enctype="multipart/form-data">
            <div class="section col">
                <p class="heading" id="heading">Sign in to your account</p>
                <p class="subheading" id="subheading"></p>
            </div>
            <div class="section col">
                <label for="user">Username Or Email</label>
                <input type="text" class="input-main" id="login" name="login" required/>
            </div>
            <div class="section col">
                <label for="pass">Password</label>
                <input type="password" class="input-main" id="pass" name="pass" required/>
            </div>
            <div class="section col">
                <button class="btn-main" type="submit">Sign In</button>
            </div>
        </form>
        <script type="text/javascript" src="/js/lib/forms/forms.js"></script>

    </div>
</body>

<?php require($root."/includes/footer.php"); ?>
