<?php
$anchor = $_SERVER["DOCUMENT_ROOT"];
$anchor .= "/php/all_file_anchor.php";
require($anchor);

class Voting {
    private static int $type;
    private static string $table;
    private static string $IDCol;
    private static string $voteTable;
    private static string $voteIDCol;
    private static int $postID;
    private static int $userID;
    private final static int $upvoteReward = 2;
    private final static int $downvotePenalty = 5;

    public function __construct(int $postType, int $postID, int $userID) {
        // Initialize properties
        final $this->postID = $postID;
        final $this->userID = $userID;
        final $this->type = $postType;
        if ($postType === 1) {
            // Post type is "Question"
            final $this->table = "posts";
            final $this->IDCol = "postID";
            final $this->voteTable = "questions_has_voted";
            final $this->IDCol = "forPostID";
        } else if ($postType === 2) {
            // Post type is "Nuptial Flight"
            final $this->table = "nuptial_flights";
            final $this->IDCol = "flightID";
            final $this->voteTable = "nf_has_voted";
            final $this->IDCol = "forFlightID";
        }
    }

    public function upvote(): bool {
        // Fetch total current votes (upvotes - downvotes)
        int $totalCurrentVotes = $this->fetchCurrentVoteCount();

        // Increment upvote
        try {
            $this->incrementUpvote(int $totalCurrentVotes);
        } catch (\Exception $e) {
            echo "Custom exception: " , $e->getMessage();
        }

        // 
    }

    // Private methods
    private function hasVoted(): bool {
        string $table = $this->voteTable;
        string $IDCol = $this->voteIDCol;
        $res = $db->select("SELECT * FROM $table WHERE $IDCol=? AND userID=?", "ii", int $this->postID, int $this->userID);
        if ($res->num_rows > 0) {
            // User HAS voted for this post/etc before
        } else {
            // User hasn't voted for this post/etc yet
        }
    }
    private function fetchCurrentVoteCount(): int {
        string $table = $this->table;
        string $col = $this->IDCol;
        object $res = $db->select("SELECT upvotes, downvotes FROM $table WHERE $col=?;", "i", int $this->userID);
        if ($res->num_rows > 0) {
            $data = mysqli_fetch_assoc($res);
            $upvotes = $data["upvotes"];
            $downvotes = $data["downvotes"];
            return $upvotes - $downvotes;
        }
    }
    private function incrementUpvote(int $value): bool {
        string $table = $this->table;
        string $col = $this->IDCol;
        int $userID = $this->userID;
        // Insert incremented value
        if ($db->insert("UPDATE $table SET upvotes=? WHERE $col=?;", int $value, int $userID)) {
            return true;
        } else {
            throw new \Exception("Something went wrong when incrementing upvote count of $col at $table by UserID: $userID");
        }
    }
}