<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");
?>

<body>
    <div class="container">
    
        <form class="form-main col main-object" id="registration-form" action="" method="POST" enctype="multipart/form-data">
            <div class="section col">
                <p class="heading" id="heading">Register for an account with us</p>
                <p class="subheading" id="subheading">Get 5 seeds just for joining!</p>
            </div>
            <div class="section col">
                <label for="user">Username</label>
                <input type="text" class="input-main" id="user" name="user" required/>
            </div>
            <div class="section col">
                <label for="em">Email Address</label>
                <input type="text" class="input-main" id="em" name="em" required/>
            </div>
            <div class="section col">
                <label for="pass">Password</label>
                <input type="password" class="input-main" id="pass" name="pass" required/>
            </div>
            <div class="section col">
                <label for="passrpt">Password</label>
                <input type="password" class="input-main" id="passrpt" name="passrpt" required/>
            </div>
            <div class="section col">
                <label for="passrpt">Profile Image (Optional)</label>
                <div class="profile-image-preview-container"><img class="profile-image-preview" src="/web_images/icons/image-select.png" id="profile-image-select"/></div>
                <input class="hidden" type="file" id="profileImage" name="profileImage" accept="image/*"/>
            </div>
            <div class="section col">
                <button class="btn-main" type="submit">Create Account</button>
            </div>
        </form>
        <script type="text/javascript" src="/js/lib/forms/forms.js"></script>

    </div>
</body>

<?php require($root."/includes/footer.php"); ?>
