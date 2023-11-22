<script type="text/javascript">
const isLoggedIn = <?php if ($isLoggedIn === true) {echo "true";} else {echo "false";}; ?>;
</script>


<div class="navbar">
    <div class="navbar-layer layer-one">
        <a class="main-button home-link" href="/">THE ANT PORTAL</a>

        <a class="main-button" href="/questions/">Questions</a>
        <a class="main-button" href="/nup_data/">Nuptial Flight Data</a>
        <a class="main-button" href="/myrecords/">My Records</a>
    </div>
    <div class="navbar-layer layer-two">
        <?php
        if ($isLoggedIn === true) {
            echo 
            '
            <a href="/users/user?userID='.$userID.'"><div class="profile-button">
                <div class="profile-image-container"><img class="profile-image" src="'.$userPFP.'"></div>
                <p class="username">'.$username.'</p>
            </div></a>
            ';
            if ($isAdmin === true) {
                echo
                '
                <div class="nav-admin-controls">
                    <div class="nav-button" id="admin-controls-trigger">
                        <div class="button-image-container"><img class="button-image" id="admin-controls-image" src="/web_images/icons/expand_more.png"/></div>
                        <p class="admin-dropdown-button">Admin Controls</p>
                    </div>
                    <div class="admin-controls-dropdown" id="admin-dropdown">
                        <a class="admin-button" href="/create_news/">Post News</a>
                        <a class="admin-button" href="#">Button Two</a>
                        <a class="admin-button" href="#">Button Three</a>
                    </div>
                </div>
                ';
            }
            echo '<a class="reg-button" id="logout-button" href="/users/logout/">Sign Out</a>';
        } else {
            echo '<a class="reg-button" href="/users/register/">Register</a>';
            echo '<a class="reg-button" href="/users/login/">Sign In</a>';
        }
        ?>
    </div>
</div>

<script type="text/javascript">
$("#admin-controls-trigger").on("click", () => {
    $("#admin-dropdown").slideToggle("fast");
    $("#admin-controls-image").toggleClass("rotate");
});
</script>