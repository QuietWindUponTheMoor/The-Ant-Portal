<?php
$anchor = $_SERVER["DOCUMENT_ROOT"];
$anchor .= "/php/main_controller.php";
require($anchor);

class Voting {
    private object $db;
    private int $type;
    private $table;
    private $IDCol;
    private $voteTable;
    private $voteIDCol;
    private int $postID;
    private int $userID;
    private int $upvoteReward = 2;
    private int $downvotePenalty = 5;

    public function __construct(object $db, int $postType, int $postID, int $userID, int $voteType) {
        // Initialize properties
        $this->db = $db;
        $this->postID = $postID;
        $this->userID = $userID;
        $this->type = $postType;
        if ($postType === 1) {
            // Post type is "Question"
            $this->table = "posts";
            $this->IDCol = "postID";
            $this->voteTable = "questions_has_voted";
            $this->voteIDCol = "forPostID";
        } else if ($postType === 2) {
            // Post type is "Nuptial Flight"
            $this->table = "nuptial_flights";
            $this->IDCol = "flightID";
            $this->voteTable = "nf_has_voted";
            $this->voteIDCol = "forFlightID";
        }

        if ($voteType === 1) {
            // User clicked upvote
            echo $this->upvote();
        } else if ($voteType === 2) {
            // User clicked downvote
            echo $this->downvote();
        } else {
            die("Something went wrong while getting the vote type. Please try again or contact an administrator.");
        }
    }

    public function upvote(): int {
        // Check if user has voted yet
        $hasVoted = $this->hasVoted();

        // Operate based on $hasVoted result
        if ($hasVoted === true) {
            // User HAS voted on this post before.
            // Get vote type:
            $voteType = $this->fetchVoteType();
            if ($voteType === 1) {
                // Original vote was an upvote:
                // Remove the vote record
                if ($this->removeVoteRecord()) {
                    // Decrement upvotes
                    if ($this->decrementUpvote()) {
                        return $this->fetchCurrentTotalVotes(); // Final
                    } else {
                        die("Something went wrong while decrementing upvotes. Please try again or contact an administrator."); // Final (error)
                    }
                } else {
                    die("Something went wrong while removing the vote record. Please try again or contact an administrator.");
                }
            } else if ($voteType === 0) {
                // Original vote was a downvote:
                // Change vote record to type of 1 (upvote)
                if ($this->changeVoteRecordType(1)) {
                    // Increment upvote & decrement downvote in the post's record
                    if ($this->incrementUpvote()) {
                        // If successfully incremented upvotes, now decrement downvotes
                        if ($this->decrementDownvote()) {
                            return $this->fetchCurrentTotalVotes(); // Final
                        } else {
                            die("Something went wrong while decrementing downvotes. Please try again or contact an administrator."); // Final (error)
                        }
                    } else {
                        die("Something went wrong while incrementing the upvote count. Please try again or contact an administrator.");
                    }
                } else {
                    die("Something went wrong while changing the vote record type. Please try again or contact an administrator.");
                }
            } else {
                die("There was an error parsing the vote type ($voteType). Please try again or contact an administrator.");
            }
        } else if ($hasVoted === false) {
            // Create vote record
            if ($this->createVoteRecord(1)) {
                // Increment upvote
                if ($this->incrementUpvote()) {
                    return $this->fetchCurrentTotalVotes(); // Final
                } else {
                    die("Something went wrong while incrementing upvotes. Please try again or contact an administrator."); // Final (error)
                }
            } else {
                die("Something went wrong while creating the vote record. Please try again or contact an administrator.");
            }
        }
    }
    public function downvote(): int {
        // Check if user has voted yet
        $hasVoted = $this->hasVoted();

        // Operate based on $hasVoted result
        if ($hasVoted === true) {
            // User HAS voted on this post before.
            // Get vote type:
            $voteType = $this->fetchVoteType();
            if ($voteType === 0) {
                // Original vote was an downvote:
                // Remove the vote record
                if ($this->removeVoteRecord()) {
                    // Decrement downvotes
                    if ($this->decrementDownvote()) {
                        return $this->fetchCurrentTotalVotes(); // Final
                    } else {
                        die("Something went wrong while decrementing downvotes. Please try again or contact an administrator."); // Final (error)
                    }
                } else {
                    die("Something went wrong while removing the vote record. Please try again or contact an administrator.");
                }
            } else if ($voteType === 1) {
                // Original vote was an upvote:
                // Change vote record to type of 0 (downvote)
                if ($this->changeVoteRecordType(0)) {
                    // Increment downvote & decrement upvote in the post's record
                    if ($this->incrementDownvote()) {
                        // If successfully incremented upvotes, now decrement downvotes
                        if ($this->decrementUpvote()) {
                            return $this->fetchCurrentTotalVotes(); // Final
                        } else {
                            die("Something went wrong while decrementing upvotes. Please try again or contact an administrator."); // Final (error)
                        }
                    } else {
                        die("Something went wrong while incrementing the downvote count. Please try again or contact an administrator.");
                    }
                } else {
                    die("Something went wrong while changing the vote record type. Please try again or contact an administrator.");
                }
            } else {
                die("There was an error parsing the vote type ($voteType). Please try again or contact an administrator.");
            }
        } else if ($hasVoted === false) {
            // Create vote record
            if ($this->createVoteRecord(0)) {
                // Increment downvote
                if ($this->incrementDownvote()) {
                    return $this->fetchCurrentTotalVotes(); // Final
                } else {
                    die("Something went wrong while incrementing downvotes. Please try again or contact an administrator."); // Final (error)
                }
            } else {
                die("Something went wrong while creating the vote record. Please try again or contact an administrator.");
            }
        }
    }

    // Private methods
    private function hasVoted(): bool {
        $table = $this->voteTable;
        $IDCol = $this->voteIDCol;
        $res = $this->db->select("SELECT * FROM $table WHERE $IDCol=? AND userID=?", "ii", $this->postID, $this->userID);
        if ($res->num_rows > 0) {
            // User HAS voted for this post/etc before
            return true;
        } else {
            // User hasn't voted for this post/etc yet
            return false;
        }
    }
    private function fetchVoteType(): int {
        $table = $this->voteTable;
        $col = $this->voteIDCol;
        $res = $this->db->select("SELECT updown FROM $table WHERE userID=? AND $col=?;", "ii", $this->userID, $this->postID);
        if ($res->num_rows > 0) {
            // Return post type
            return mysqli_fetch_assoc($res)["updown"];
        } else {
            die("Something went wrong with fetching the vote type. Please try again or contact an administrator.");
        }
    }
    private function removeVoteRecord(): bool {
        $table = $this->voteTable;
        $col = $this->voteIDCol;
        if ($this->db->insert("DELETE FROM $table WHERE userID=? AND $col=?;", "ii", $this->userID, $this->postID)) {
            return true;
        } else {
            return false;
        }
    }
    private function changeVoteRecordType(int $type): bool {
        $table = $this->voteTable;
        $col = $this->voteIDCol;
        return $this->db->insert("UPDATE $table SET updown=? WHERE userID=? AND $col=?;", "iii", $type, $this->userID, $this->postID);
    }
    private function incrementUpvote(): bool {
        $table = $this->table;
        $col = $this->IDCol;
        // Fetch current upvote count
        $currentUpvoteCount = $this->fetchUpvoteCount();
        // Calculate new upvote count (current + 1)
        $newUpvoteCount = $currentUpvoteCount + 1;
        return $this->db->insert("UPDATE $table SET upvotes=? WHERE $col=?;", "ii", $newUpvoteCount, $this->postID);
    }
    private function incrementDownvote(): bool {
        $table = $this->table;
        $col = $this->IDCol;
        // Fetch current downvote count
        $currentDownvoteCount = $this->fetchDownvoteCount();
        // Calculate new downvote count (current + 1)
        $newDownvoteCount = $currentDownvoteCount + 1;
        return $this->db->insert("UPDATE $table SET downvotes=? WHERE $col=?;", "ii", $newDownvoteCount, $this->postID);
    }
    private function decrementUpvote(): bool {
        $table = $this->table;
        $col = $this->IDCol;
        // Fetch current upvote count
        $currentUpvoteCount = $this->fetchUpvoteCount();
        // Calculate new upvote count (current - 1)
        $newUpvoteCount = $currentUpvoteCount - 1;
        return $this->db->insert("UPDATE $table SET upvotes=? WHERE $col=?;", "ii", $newUpvoteCount, $this->postID);
    }
    private function decrementDownvote(): bool {
        $table = $this->table;
        $col = $this->IDCol;
        // Fetch current downvote count
        $currentDownvoteCount = $this->fetchDownvoteCount();
        // Calculate new downvote count (current - 1)
        $newDownvoteCount = $currentDownvoteCount - 1;
        return $this->db->insert("UPDATE $table SET downvotes=? WHERE $col=?;", "ii", $newDownvoteCount, $this->postID);
    }
    private function fetchUpvoteCount(): int {
        $table = $this->table;
        $col = $this->IDCol;
        $res = $this->db->select("SELECT upvotes FROM $table WHERE $col=?;", "i", $this->postID);
        if ($res->num_rows > 0) {
            return mysqli_fetch_assoc($res)["upvotes"];
        } else {
            die("Something went wrong with fetching current upvote count. Please try again or contact an administrator.");
        }
    }
    private function fetchDownvoteCount(): int {
        $table = $this->table;
        $col = $this->IDCol;
        $res = $this->db->select("SELECT downvotes FROM $table WHERE $col=?;", "i", $this->postID);
        if ($res->num_rows > 0) {
            return mysqli_fetch_assoc($res)["downvotes"];
        } else {
            die("Something went wrong with fetching current downvote count. Please try again or contact an administrator.");
        }
    }
    private function createVoteRecord(int $updown): bool {
        $table = $this->voteTable;
        $col = $this->voteIDCol;
        return $this->db->insert("INSERT INTO $table (forFlightID, userID, updown) VALUES (?, ?, ?);", "iii", $this->postID, $this->userID, $updown);
    }
    private function fetchCurrentTotalVotes(): int {
        $table = $this->table;
        $col = $this->IDCol;
        $res = $this->db->select("SELECT upvotes, downvotes FROM $table WHERE $col=?;", "i", $this->postID);
        if ($res->num_rows > 0) {
            $data = mysqli_fetch_assoc($res);
            $upvotes = $data["upvotes"];
            $downvotes = $data["downvotes"];
            return $upvotes - $downvotes;
        } else {
            die("Something went wrong fetching current total votes. Please try again or contact an administrator.");
        }
    }
}