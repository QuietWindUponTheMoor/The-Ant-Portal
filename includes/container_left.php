<div class="container_left">

    <div class="news" id="news-top">
        <div class="collapse">
            <p class="title">Site news!</p>
            <a class="title" id="news-collapse" type="button">-</a>
        </div>
    </div>
    <div class="news" id="news-bottom">
        <?php
        $newsRes = $db->selectAll("SELECT * FROM news ORDER BY newsID LIMIT 3;");
        if ($newsRes->num_rows > 0) {
            while ($news = mysqli_fetch_assoc($newsRes)) {
                $mainTitle = $news["mainTitle"];
                $newsDatetime = $news["datetime"];

                // Process topics/subjects
                $topicsArray = explode("‡ ", $news["topics"]);
                $topicSubjectsArray = explode("‡ ", $news["topicSubjects"]);

                // Echo news
                echo
                '
                <div class="news_item">
                    <div class="top">
                        <p class="item_title">'.$mainTitle.'</p>
                        <p class="item_date">'.$newsDatetime.'</p>
                    </div>
                    <div class="bottom">
                        ';
                        foreach ($topicsArray as $key => $topic) {
                            generateTopicAndSubject($topic, $topicSubjectsArray[$key]);
                        }
                        echo '
                    </div>
                </div>
                ';
            }
        }

        function generateTopicAndSubject(string $topicTitle, string $topicSubject) {
            echo
            '
            <div class="section">
                <p class="section-title">'.$topicTitle.'</p>
                <ol>
                    <li>'.$topicSubject.'</li>
                </ol>
            </div>
            ';
        }
        ?>


    </div>

</div>