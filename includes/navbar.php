<div class="nav col">
    <div class="nav-sub row">
        <div class="main row">
            <a class="site-title nav-hover-animation" href="/">The Ant Portal</a>
            <a class="button nav-hover-animation" href="#">Btn 1</a>
            <a class="button nav-hover-animation" href="#">Btn 2</a>
            <a class="button nav-hover-animation" href="#">Btn 3</a>
        </div>


        <div class="secondary account row">

            <div class="button nav-hover-animation" id="account-trigger">
                <img class="nav-dropdown-image" id="account-dropdown-image" src="/web_images/icons/expand_more.png"/>
                <p class="label">Your Account</p>
            </div>
                <div class="dropdown col" id="account-dropdown">
                    <div class="heading-container">
                        <?php
                        if ($isLoggedIn === true && isset($_SESSION["user_id"]) && isset($_SESSION["username"])) {
                            echo 
                            '<p class="heading">Welcome,</p><a class="username" id="account-name" href="/users/1/user?id='.$_SESSION["user_id"].'">'.$_SESSION["username"].'</a>';
                            
                        } else {
                            echo '<p class="heading">You Need To Sign In</p>';
                        }
                        ?>
                    </div>
                    <?php 
                    if ($isLoggedIn === true && isset($_SESSION["profile_image"]) && isset($_SESSION["user_id"])) {
                        echo
                        '
                        <div class="dropdown-section user-image-section">
                            <div class="user-image-container"><a href="/users/1/user?id='.$_SESSION["user_id"].'"><img class="user-image" id="user-image" src="'.$_SESSION["profile_image"].'" title="Visit your profile"/></a></div>
                        </div>
                        ';
                    }
                    ?>
                    <div class="dropdown-section user-badges">
                        <p class="label">BADGES</p>
                    </div>
                    <div class="dropdown-section row login-status-section">
                        <?php
                        if ($isLoggedIn === true) {
                            echo '<a class="reg-button" id="signout" href="/users/0/signout">Sign Out</a>';
                        } else {
                            echo '<a class="reg-button" href="/users/0/signin">Sign In</a><a class="reg-button" href="/users/0/register">Register</a>';
                        }
                        ?>
                    </div>
                </div>
        </div>
            


    </div>
</div>

<script type="text/javascript">const isLoggedIn = <?php echo $isLoggedIn; ?>;</script>

<script type="text/javascript" src="/js/lib/page_control/navbar.js"></script>