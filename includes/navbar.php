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
            echo '<a class="reg-button" id="logout-button" href="/users/logout/">Sign Out</a>';
        } else {
            echo '<a class="reg-button" href="/users/register/">Register</a>';
            echo '<a class="reg-button" href="/users/login/">Sign In</a>';
        }
        ?>
    </div>
</div>