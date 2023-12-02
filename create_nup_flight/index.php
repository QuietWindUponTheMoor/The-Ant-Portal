<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require($root."/includes/head.php");
require($root."/includes/navbar.php");

if ($isLoggedIn === false) {
    header("Location: /");
}
?>

<body>
    <div class="container">
        <div class="container_sub">
            <?php require($root."/includes/container_left.php"); ?>
            <div class="container_main">



                <form class="form-main" id="nup-flight-form" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <p class="form-title">Submit A Nuptial Flight Observation</p>
                        <p class="response" id="error"></p>
                        <p class="response" id="warning"></p>
                        <p class="response" id="success"></p>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="species">What species did you observe? Make sure the species is spelled/formatted correctly.</label>
                        <label>The first word (genus) should be capitalized, followed by a space; the second word (species) should be all-lowercased.</label>
                        <input class="input-main" type="text" id="species" name="species" minlength="5" maxlength="256" placeholder="Example: Lasius neoniger" required/>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="body">Describe this flight and any relevant details.</label>
                        <textarea class="input-main" id="body" name="body" minlength="5" maxlength="30000" placeholder="Example: It was sunny and about 78 degrees fahrenheit (F) outside with little wind." required></textarea>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="date">Enter the date of the observation.</label>
                        <label>Please use the formatt YYYY-DD-MM, like shown below:</label>
                        <input class="input-main" type="text" id="date" name="date" minlength="10" maxlength="10" placeholder="Example: 2023-16-06" required/>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="time">Enter the time of the observation.</label>
                        <label>Please use the formatt HH:MMam/pm, like shown below:</label>
                        <input class="input-main" type="text" id="time" name="time" minlength="7" maxlength="7" placeholder="Example: 06:43pm" required/>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="time">Enter the outdoor temperature at the time of the observation.</label>
                        <label>You can use C or F for either celcius or fahrenheit. Please format like shown below (this recording will be translated to celcius or fahrenheit):</label>
                        <input class="input-main" type="text" id="temperature" name="temperature" minlength="3" maxlength="4" placeholder="Example: 76F" required/>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="wind-speed">Please enter the wind speed during the observation.</label>
                        <label>Enter the wind speed as just a number in mph (miles-per-hour). If there was no wind during the observation, please enter "0". Or, if you'd like to opt out, please enter "n/a".</label>
                        <label>You can use a conversion tool below:</label>
                        <label><a class="label-link" href="https://www.unitconverters.net/speed/kph-to-mph.htm" target="_blank">kmph -> mph conversion tool</a></label>
                        <input class="input-main" type="text" id="wind-speed" name="wind-speed" minlength="1" maxlength="3" placeholder="Example: 5" required/>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="moon-cycle">Enter the moon cycle at the time of the observation.</label>
                        <label>Please use the format of "waning-gibbous" and not "waning gibbous" as examples.</label>
                        <label>Please enter "n/a" if you don't know, or want to opt out. Additionally, you can use the tool below to find out:</label>
                        <label><a class="label-link" href="https://nineplanets.org/moon/phase/today/" target="_blank">What is the moon cycle today?</a></label>
                        <input class="input-main" type="text" id="moon-cycle" name="moon-cycle" minlength="3" maxlength="50" placeholder="Example: waning-gibbous" required/>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="images">Select any images you have of the observation. (Optional)</label>
                        <input class="hidden" type="file" id="images" name="images[]" accept="image/*" multiple/>
                        <button class="btn-action" type="button" id="select-images" onclick="$('#images').click();">Add Images</button>
                    </div>
                    <div class="form-section" id="tags-container">
                        <label class="main-label" for="tags">Enter relevant tags (separated by spaces or commas):</label>
                        <label>Tags cannot contain non-alphabetical [a-Z] characters. Tags are separated by a space, or a comma: ','.</label>
                        <!-- Max of 5 tags -->
                        <input class="input-main" type="text" id="tags" name="tagsBefore" placeholder="Example: observation, tetramorium"/>
                        <input class="hidden" type="text" id="hidden-tags" name="final-tags" value="null"/>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="selected-tags">Selected tags. Click to delete.</label>
                        <div class="tags-list" id="selected-tags"></div>
                    </div>
                    <div class="form-section hidden" id="hidden-fields">
                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $userID; ?>" required/>
                        <input type="hidden" id="database" name="database" value="<?php echo $dbHost; ?>" required/>
                    </div>
                    <div class="final-section">
                        <button class="btn-secondary" id="cancel" type="button" onclick="window.history.back();">Cancel Post</button>
                        <button class="btn-warning" id="reset" type="reset">Reset Form</button>
                        <button class="btn-main" id="submit" type="button">Submit Nuptial Flight</button>
                    </div>
                </form>



            </div>
            <?php require($root."/includes/container_right.php"); ?>
        </div>
    </div>
</body>
<script type="text/javascript" src="/js/lib/post_creation/nup_flights.js"></script>

<?php require($root."/includes/footer.php"); ?>