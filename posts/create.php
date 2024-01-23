<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");

if ($isLoggedIn !== true) {
    header("/redirects/not_signed_in.php");
}
?>

<body>
    <div class="container">
    
        <form class="form-main col main-object" id="post-creation-form" action="" method="POST" enctype="multipart/form-data">
            <div class="section col">
                <p class="heading" id="heading">Create A Post</p>
                <p class="subheading" id="subheading"></p>
            </div>
            <div class="section col">
                <label for="user">What type of post would you like to create?</label>
                <div class="row" style="align-items: center; gap: 3rem; flex-wrap: wrap;">
                    <!--
                    Types:
                    0 = Question
                    1 = General
                    2 = informative
                    3 = observation
                    4 = nuptial_flight
                    -->
                    <div class="row" style="align-items: center;">
                        <input class="type-select" type="radio" id="type-question" name="type" value="0" required/>
                        <label for="type-question">Question</label>
                    </div>
                    <div class="row" style="align-items: center;">
                        <input class="type-select" type="radio" id="type-general" name="type" value="1" required/>
                        <label for="type-general">General</label>
                    </div>
                    <div class="row" style="align-items: center;">
                        <input class="type-select" type="radio" id="type-informative" name="type" value="2" required/>
                        <label for="type-informative">Informative</label>
                    </div>
                    <div class="row" style="align-items: center;">
                        <input class="type-select" type="radio" id="type-observation" name="type" value="3" required/>
                        <label for="type-observation">Observation</label>
                    </div>
                    <div class="row" style="align-items: center;">
                        <input class="type-select" type="radio" id="type-nuptial-flight" name="type" value="4" required/>
                        <label for="type-nuptial-flight">Nuptial Flight</label>
                    </div>
                </div>
            </div>
            <div class="fields-list col" id="fields-list"></div>
            <input type="hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>" required/>
        </form>

    </div>
</body>
<script type="text/javascript" src="/js/lib/posts/create.js"></script>
<script type="text/javascript" src="/js/lib/posts/type_select.js"></script>

<?php require($root."/includes/footer.php"); ?>