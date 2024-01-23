// Initialize vars
has_voted = false;
previous_vote_type = null;
times_voted = 0;

// Listen for vote activation
$("#post-page-container").on("click", ".vote-image", async function () {
    // Get the ID for the vote button
    const button_id = $(this).attr("id");
    // Get the post/answer type information
    const regex = /^(upvote|downvote)(?:-for-([\da-f]+))?$/;
    const match = button_id.match(regex);

    /* Initialize vote_type and vote_for_type
    vote_type would be either 'upvote' or 'downvote',
    vote_for_type would be either 0 or 1 (integers)
    -----
    vote_type:
    0 = downvote
    1 = upvote
    -----
    vote_for_type:
    0 = For a post, the original post
    1 = For the answer of a question
    2 = For the reply/comment of an post (original)'s comment/reply
    3 = For an question's answer's reply/comment
    -----
    */

    let vote_type = null;
    let vote_for_type = null;
    let answerID = null;

    if (match) { // If the ID matches the regex pattern
        const tmp_vote_type = match[1];
        if (tmp_vote_type === "upvote") {
            vote_type = 1;
        } else if (tmp_vote_type === "downvote") {
            vote_type = 0;
        } else {
            console.error(`There was an error matching the vote_type with value: ${tmp_vote_type}`);
        }

        if (match[2]) { // If there was a match for answer ID, also meaning this post is a question that has answers
            vote_for_type = 1;
            answerID = match[2];
        } else {
            vote_for_type = 0;
        }
    } else {
        console.error(`There was an error matching regex pattern for vote button ID of '${this.button_id}'`);
    }

    // Initialize Voting class
    const Voting = new PostVoting(post_id, thisUserID, vote_type, vote_for_type, answerID);


    // Update the value in real time
    // First get the vote-count element
    const voteCountElement = Voting.$targetVoteCountElement($(this), ".vote-count");
    currentVoteCount = parseInt(voteCountElement.text());
    let newVoteCount = null;

    if (has_voted) { // If the user voted prior to this
        if (times_voted < 3) {
            if (previous_vote_type === vote_type) { // If the vote was the same as before
                if (vote_type === 1) { // If previous vote type was upvote
                    newVoteCount = currentVoteCount - 1;
                    // Now reset
                    has_voted = false;
                    previous_vote_type = null;
                } else { // If previous vote type was downvote
                    newVoteCount = currentVoteCount + 1;
                    // Now reset
                    has_voted = false;
                    previous_vote_type = null;
                }
            } else if (previous_vote_type !== vote_type) { // If the vote was not the same as before
                // Do not need to do extra checks as that's already done with this parent else-if statement
                // Since previous_vote_type is already checked to see if it's the same as vote_type and it's not otherwise this statement doesn't run anyway
                if (previous_vote_type === 1) { // If previous vote type was upvote
                    newVoteCount = currentVoteCount - 2; // Decrement by two to simulate changing vote type
                    // Now reset
                    has_voted = false;
                    previous_vote_type = null;
                } else { // If previous vote type was downvote
                    newVoteCount = currentVoteCount + 2; // Increment by two to simulate changing vote type
                    // Now reset
                    has_voted = false;
                    previous_vote_type = null;
                }
            }
        } else {
            // Rate limit the user to prevent abuse.
            alert("You are being rate limited.");
            newVoteCount = currentVoteCount;
        }
        
    } else {
        if (vote_type === 0) { // Vote was downvote
            // Decrement
            newVoteCount = currentVoteCount - 1;
        } else if (vote_type === 1) { // Vote was upvote
            // Increment
            newVoteCount = currentVoteCount + 1;
        } else {
            console.error("Could not parse vote-count element's integer.");
            return; // ABORT!
        }

        // Set has_voted to true & previous vote type
        has_voted = true;
        previous_vote_type = vote_type;
    }
    
    // Update the value
    console.log(currentVoteCount, newVoteCount);
    voteCountElement.text(newVoteCount);

    // Process vote
    await Voting.processVote();

    // Increment times voted
    times_voted++;

    // Manage vote-count colors again
    manageVoteCountColors();
});