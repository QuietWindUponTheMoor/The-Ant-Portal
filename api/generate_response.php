<?php

class Responses {
    public string $webLink;
    public array $resCodes = array(
        "success" => 1,
        "invalidReqItem" => 5000,
        "invalidLength" => 5001,

    );

    public function __construct($websiteLink) {
        $this->webLink = $websiteLink;
    }

    public function invalidReq($reqItem) {
        $errorCode = $this->resCodes["invalidReqItem"];
        return "{\"error\": {
            \"message\": \"The request '$reqItem' is invalid.\",
            \"error_code\": $errorCode
        }}";
    }

    public function invalidMaxLength($reqItem) {
        $errorCode = $this->resCodes["invalidLength"];
        return "{\"error\": {
            \"message\": \"The max_length provided is invalid. Please provide a number between 1-100. You provided '$reqItem'.\",
            \"error_code\": $errorCode
        }}";
    }

    public function sendPostData(int $postID, string $title, string $body, string $imageArray, int $upvotes, int $downvotes, array $tagsArray, int $editedByUserID) {
        $postLink = $this->webLink."/posts?postID=$postID";
        $tags = implode(", ", $tagsArray);
        return '{"success": {
            "url": "'.$postLink.'",
            "post_id": '.$postID.',
            "title": "'.$title.'",
            "body": "'.$body.'",
            "attached_images": ['.$imageArray.'],
            "upvotes": '.$upvotes.',
            "downvotes": '.$downvotes.',
            "tags": ['.$tags.'],
            "last_edited_by": '.$editedByUserID.'
        }}';
    }
};