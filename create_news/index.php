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
                
                <form class="form-main" id="nup-flight-form" action="" method="POST" enctype="multipart/form-data">
                    <div class="form-section">
                        <p class="form-title">Submit A News Post</p>
                        <?php
                        if (isset($_POST["submit"])) {
                            $data = array_filter($_POST);
                            $mainTitle = $data["main-title"];
                            $topics = $data["topic-titles"];
                            $topicSubjects = $data["topic-subjects"];
                            // Get datetime
                            $datetime = date("M d, Y");

                            // Initialize topics arrays
                            $topicsArray = [];
                            $topicSubjectsArray = [];

                            foreach ($topics as $key => $topicTitle) {
                                $new_title = $topicTitle;
                                $new_subject = $topicSubjects[$key];
                                array_push($topicsArray, $new_title);
                                array_push($topicSubjectsArray, $new_subject);
                            }

                            // Finally, get values as string
                            $topics = implode("‡ ", $topicsArray);
                            $topicSubjects = implode("‡ ", $topicSubjectsArray);

                            if ($db->insert("INSERT INTO news (mainTitle, topics, topicSubjects, `datetime`) VALUES (?, ?, ?, ?);", "ssss", $mainTitle, $topics, $topicSubjects, $datetime)) {
                                echo '<p class="response" id="success">You have successfully created a news post. You can create another by clearing the form, or go elsewhere on the site.</p>';
                            } else {
                                echo '<p class="response" id="error">There was an error creating the news post, please try again or contact a developer.</p>';
                            }
                        }
                        ?>
                    </div>
                    <div class="final-section">
                        <button class="btn-main" id="add-field" type="button">Insert New Topic Title</button>
                    </div>
                    <div class="form-section">
                        <label class="main-label" for="species">Create a main title.</label>
                        <input class="input-main" type="text" id="main-title" name="main-title" minlength="5" maxlength="24" placeholder="Example: Holiday Announcement!" required/>
                    </div>

                    <div class="form-section-dynamic-fields" id="topic-titles-container">
                        <div class="form-section" id="topic-title-container-1">
                            <label class="main-label" for="topic-title-1">Topic Title #1</label>
                            <input class="input-main" type="text" id="topic-title-1" name="topic-titles[]" minlength="5" maxlength="24" placeholder="Example: Upcoming Events" required/>
                            <label class="main-label" for="topic-title-1">Topic Subject #1</label>
                            <input class="input-main" type="text" id="topic-subject-1" name="topic-subjects[]" minlength="5" maxlength="256" placeholder="Example: Thankgsiving Celebration" required/>
                        </div>
                    </div>
                    
                    <div class="final-section">
                        <button class="btn-secondary" id="cancel" type="button" onclick="window.history.back();">Cancel Post</button>
                        <button class="btn-warning" id="reset" type="reset">Reset Form</button>
                        <button class="btn-main" id="submit" name="submit" type="submit">Submit News</button>
                    </div>
                </script>
            
            </div>
            <?php require($root."/includes/container_right.php"); ?>
        </div>
    </div>
</body>
<script type="text/javascript">
$("#add-field").on("click", () => {
    // Get fields container and number of fields
    const fields_container = $("#topic-titles-container");
    let num_fields = fields_container.children().length;
    let new_field_num = (num_fields + 1);

    let template = 
    `
    <div class="form-section" id="topic-title-container-${new_field_num}">
        <label class="main-label" for="topic-title-${new_field_num}">Topic Title #${new_field_num}</label>
        <input class="input-main" type="text" id="topic-title-${new_field_num}" name="topic-titles[]" minlength="5" maxlength="24" placeholder="Example: Upcoming Events" required/>
        <label class="main-label" for="topic-title-${new_field_num}">Topic Subject #${new_field_num}</label>
        <input class="input-main" type="text" id="topic-subject-${new_field_num}" name="topic-subjects[]" minlength="5" maxlength="256" placeholder="Example: Thankgsiving Celebration" required/>
    </div>
    `;
    fields_container.append(template);
});
</script>

<?php require($root."/includes/footer.php"); ?>