// Create listeners/triggers
/*
itemType/forType:
0 = Comment/reply for a post
5 = Comment/reply for an answer to a question (only applies to questions)
*/
$("#post-page-container").on("click", "#create-reply", async () => {
    await manageReplyCreation(0, post_id, thisUserID);
}); // Manually click submit button
$("#post-page-container").on("keyup", "#reply-textbox", async (event) => {
    if (event.key === "Enter") {
        await manageReplyCreation(0, post_id, thisUserID);
    }
}); // Press enter while typing


async function manageReplyCreation(forType, itemID, userID) {
    // Invoke Comment class
    const Com = new PostComments(itemID, forType, userID);
    const $textbox = $("#reply-textbox");
    const textbox_val = $textbox.val();
    switch (forType) {
        case 0:
            textbox_val !== "" && await Com.create(textbox_val);
            Com.append($("#main-replies-list"), $textbox);
            break;
        default:
            break;
    }
}