<div class="flag-image-container">
    <img class="flag-image" id="flag-image" src="/web_images/icons/flag.png" title="Flag this post to let us know something is wrong."/>
    <div class="flagging-modal modal-main">
        <form class="modal-section flagging-form" id="flagging-form" action="" method="POST">
            <div class="modal-sub-section">
                <p class="modal-title" id="flagging-response">Please tell us what you're flagging this content for.</p>
            </div>
            <div class="modal-sub-section radio-section">
                <input type="radio" id="irrelevant-content" name="flag-reason" value="This post is irrelevant (spam, off-topic, etc)" required/>
                <label for="irrelevant-content">This post is irrelevant (spam, off-topic, etc)</label>
            </div>
            <div class="modal-sub-section radio-section">
                <input type="radio" id="confusing-or-misguided" name="flag-reason" value="This post is too confusing or misguided" required/>
                <label for="confusing-or-misguided">This post is too confusing or misguided</label>
            </div>
            <div class="modal-sub-section radio-section">
                <input type="radio" id="illegals" name="flag-reason" value="This post promotes the act of buying, selling, or trading illegal (non-native to the OP's location) ants, without proper permits." required/>
                <label for="illegals">This post promotes the act of buying, selling, or trading illegal (non-native to the OP's location) ants, without proper permits.</label>
            </div>
            <div class="modal-sub-section radio-section">
                <input type="radio" id="bullying" name="flag-reason" value="This post promotes the act of bullying (racism, homophobia, etc)" required/>
                <label for="bullying">This post promotes the act of bullying (racism, homophobia, etc)</label>
            </div>
            <div class="modal-sub-section radio-section">
                <input type="radio" id="rude-or-abusive" name="flag-reason" value="This post has rude or abusive content." required/>
                <label for="rude-or-abusive">This post has rude or abusive content.</label>
            </div>
            <div class="modal-sub-section radio-section">
                <input type="radio" id="duplicate" name="flag-reason" value="This post is a duplicate of another post or is too similar to another post to be relevant." required/>
                <label for="duplicate">This post is a duplicate of another post or is too similar to another post to be relevant.</label>
            </div>
            <div class="modal-sub-section radio-section">
                <input type="radio" id="plagiarism" name="flag-reason" value="This post contains plagiarized content from elsewhere." required/>
                <label for="plagiarism">This post contains plagiarized content from elsewhere.</label>
            </div>
            <div class="modal-sub-section radio-section">
                <input type="radio" id="something-else" name="flag-reason" value="Something else (Please write the reason below)." required/>
                <label for="something-else">Something else (Please write the reason below).</label>
            </div>
            <div class="modal-sub-section explanation-section">
                <label for="explanation">Explain your report or add additional info here. If your post is in regards to plagiarism, please provide a link. If there is a particular user, answer or reply at fault, please mention those here. If this is a duplicate to a post, please provide a link to the post it's a duplicate of. If it is someone's comment or answer you are flagging, please quote the particular text that warrants your report.</label>
                <textarea class="input-main" type="text" id="explanation" name="explanation" minlength="35" maxlength="30000" placeholder="Please explain more about your report here." required></textarea>
            </div>
            <div class="modal-sub-section final-section">
                <input type="hidden" id="user-id" name="userID" value="<?php echo $userID; ?>" required/>
                <input type="hidden" id="db" name="db" value="<?php echo $dbHost; ?>" required/>
                <button class="btn-secondary cancel-flagging" id="cancel-flagging" type="button">Cancel</button>
                <button class="btn-main" id="submit-flagging" type="submit" name="submit">Submit Flag</button>
            </div>
        </form>
    </div>
</div>