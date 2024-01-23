


// Functions list
function timeCalc(timestamp, format) { // Edit this one a bit. It works for now though (especially with .substr() being deprecated)
    /* AVAILABLE FORMATS:
    MM-DD-YYYY
    MM-DD-YYYY HH:MMa
    MM-DD-YY HH:MMa
    HH:MMa
    */
    const date = new Date(timestamp);
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const ampm = hours >= 12 ? 'pm' : 'am';
  
    const padZero = (num) => (num < 10 ? '0' + num : num);
  
    const formattedDate = format
      .replace('MM', padZero(date.getMonth() + 1))
      .replace('DD', padZero(date.getDate()))
      .replace('YYYY', date.getFullYear())
      .replace('YY', date.getFullYear().toString().substr(-2))
      .replace('HH', padZero(hours % 12 || 12))
      .replace('MM', padZero(minutes))
      .replace('a', ampm.toLowerCase());
  
    return formattedDate;
}

async function query(desQuery, data, __callback) {
  
    // Disguise as form_data
    const form_data = new FormData();
    // Append the argument data to form_data
    form_data.append("query", desQuery);
    form_data.append("data", JSON.stringify(data)); // Stringify the JSON data

    try {
        // Make request
        const response = await fetch(API_addr + encodeURIComponent("manual_queries"), {
            method: "POST",
            body: form_data,
            headers: {
              "Accept": "application/json"
            },
        });

        if (response.ok) {
            // Parse and return
            return await response.json();
        } else {
            throw new Error(`Request failed: ${response.statusText}`);
        }
    } catch (error) {
        const err_str = `Request failed: ${error}`;
        console.error(err_str);
        throw err_str;
    }/*

    // Make request
    await $.ajax({  
        type: "POST",  
        url: API_addr + encodeURIComponent("manual_queries"), 
        data: form_data,
        processData: false,
        contentType: false,
        success: function(response) {
          __callback(response);
        }
    });*/
}

class PostVoting {
    itemID = null;
    userID = null;
    button_id = null;
    vote_type = null;
    vote_for_type = null;

    // Dynamic (from vote data)
    updown = null;

    constructor(itemID, userID, vote_type, vote_for_type, answerID) {
        // Set itemID and userID
        this.itemID = itemID ?? (() => {throw new Error(`Post ID could not be found: ${itemID}`);})();
        this.userID = userID ?? (() => {throw new Error(`User ID could not be found: ${userID}`);})();
        // Set the vote data
        this.vote_type = vote_type;
        this.vote_for_type = vote_for_type;
        if (vote_for_type === 1) { // If the vote was for an answer to a question
            // Set the itemID to the answer ID
            this.itemID = answerID;
        }
    }

    compareVotes(newVote, previousVote) { // Returns true if the previous vote is the same as the previous vote
        if (newVote === previousVote) {
            return true;
        } else {
            return false;
        }
    }

    async hasVoted() {
        try {
            const voteData = await query("SELECT * FROM voting WHERE itemID=? AND userID=? AND `type`=?;", [this.itemID, this.userID, this.vote_for_type]);
            if (voteData.data === null) {
                return false; // Has not voted for this yet
            } else {
                this.updown = voteData.data.updown;
                return true; // Has voted for this before
            }
        } catch (error) {
            console.error(`There was an issue fetching 'has-voted' data: ${error}`);
        }
    }

    async setVote(updown) {
        /*
        0 = downvote
        1 = upvote
        */
        try {
            const voteData = await query("INSERT INTO voting (itemID, userID, `type`, updown) VALUES (?, ?, ?, ?);", [this.itemID, this.userID, this.vote_for_type, updown]);
            if (voteData.status !== 200) {
                return false; // Something went wrong
            } else {
                return true; // Successfully changed vote type
            }
        } catch (error) {
            console.error(`There was an issue creating the vote: ${error}`);
        }
    }

    async removeVote() {
        try {
            const voteData = await query("DELETE FROM voting WHERE itemID=? AND userID=? AND `type`=?;", [this.itemID, this.userID, this.vote_for_type]);
            if (voteData.status !== 200) {
                return false; // Something went wrong
            } else {
                return true; // Successfully changed vote type
            }
        } catch (error) {
            console.error(`There was an issue deleting the vote: ${error}`);
        }
    }

    async changeVoteType(newUpdown) {
        /*
        0 = downvote
        1 = upvote
        */
       let vote_removed = null;

        const votesMatch = this.compareVotes(newUpdown /* <-- same as this.vote_type */, this.updown);
        if (votesMatch) { // User already voted & did so again for the same type
            // Remove vote (cancel)
            vote_removed = await this.removeVote();
        } else { // User already voted but changed what type of vote they wanted
            // Change the vote type
            try {
                const voteData = await query("UPDATE voting SET updown=? WHERE itemID=? AND userID=? AND `type`=?;", [newUpdown, this.itemID, this.userID, this.vote_for_type]);
                if (voteData.status !== 200) {
                    return false; // Something went wrong
                } else {
                    return true; // Successfully changed vote type
                }
            } catch (error) {
                console.error(`There was an issue changing 'vote-type': ${error}`);
            }
        }
    }

    async processVote() {
        // Initialize vars
        let has_voted = null;
        let vote_changed = null;
        let vote_inserted = null;

        // Check if user has voted for this already
        has_voted = await this.hasVoted();

        if (has_voted === true) { // User has voted already
            vote_changed = await this.changeVoteType(this.vote_type);
        } else { // The user hasn't voted yet
            vote_inserted = await this.setVote(this.vote_type);
        }
    }

    // List of methods not always invoked from processVote():

    $targetVoteCountElement($startEl, search) {
        // Find the parent element of the starting element
        const $parent = $startEl.parent();
        // Find the sibling of the element with specific ID or class
        const $siblings = $parent.siblings(search);
        // Get the first match
        const $target = $siblings.first();
        // Now return
        return $target;
    }
}