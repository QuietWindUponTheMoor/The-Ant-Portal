<script type="text/javascript">
const isLoggedIn = <?php if ($isLoggedIn === true) {echo "true";} else {echo "false";}; ?>;
</script>

<?php
// Badge links & names
require($_SERVER["DOCUMENT_ROOT"]."/php/settings/badges.php");
$ownedBadges = [];

if ($isLoggedIn === true) {
    $badgeRes = $db->select("SELECT badge FROM owned_badges WHERE userID=?;", "i", $userID);
    if ($badgeRes->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($badgeRes)) {
            $badgeID = $row["badge"];
            array_push($ownedBadges, $badgeID);
        }
    }

    // Save new array values
    $ownedBadges = $ownedBadges;
    $output = []; // Initialize output

    foreach ($badges as $badge) {
        // If badge with this ID exists in $badges
        if (in_array($badge['id'], $ownedBadges)) {
            // Push value to output array
            $output[$badge["name"]] = $badge;
        }
    }

    $badgesOwned = getLastBadgeOfEachType($output);

    /*
    EXAMPLE OF $badgesOwned format/structure:
    $badgesOwned = [ 
        "age" => [
            "id" => 2,
            "type" => "age",
            "name" => "intermediate",
            "cost" => 5000,
            "link" => "/web_images/icons/badges/intermediate.png",
        ],
        "rep" => [
            "id" => 6,
            "type" => "rep",
            "name" => "approver",
            "cost" => 5000,
            "link" => "/web_images/icons/badges/approver.png",
        ],
    ];*/
}


function getLastBadgeOfEachType(array $badges): array {
    $lastOfType = [];

    // Iterate through the array
    foreach ($badges as $badge) {
        // Assign the current badge to the corresponding type
        $lastOfType[$badge["type"]] = $badge;
    }
    
    // Reformat the array to have type as keys
    $newArray = [];
    foreach ($lastOfType as $type => $badge) {
        $newArray[$type] = $badge;
    }
    
    return $newArray;
}

function displayBadgesForUsername(array $badgesOfLastOfType): string {
    // Simplify array var name
    $array = $badgesOfLastOfType;
    // Initialize output array
    $output = [];

    // Iterate over each
    foreach ($array as $badge) {
        $badgeID = $badge["id"];
        $name = $badge["name"];
        $link = $badge["link"];
        array_push($output, '<div class="username-badge-container" title="Badge: '.$name.'"><img class="username-badge" src="'.$link.'"/></div>');
    }

    return implode("", $output);
}
?>

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
                <p class="username">'.$username.'</p>';
                echo displayBadgesForUsername($badgesOwned);
            echo '</div></a>
            ';
            if ($isAdmin === true) {
                echo
                '
                <div class="nav-admin-controls">
                    <div class="nav-button" id="admin-controls-trigger">
                        <div class="button-image-container"><img class="button-image" id="admin-controls-image" src="/web_images/icons/expand_more.png"/></div>
                        <p class="admin-dropdown-button">Admin Actions</p>
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