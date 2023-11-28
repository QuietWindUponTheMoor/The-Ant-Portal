var PostEditing = /** @class */ (function () {
    function PostEditing() {
        // Static
        this.$b_container = $(".body-container");
        this.$body = $(".body");
        this.$editing_body = $(".body-editing-mode");
    }
    PostEditing.prototype.swapBodyWithTextbox = function () {
        // Get the text of the body & save it
        this.saved_body_text = this.$body.text();
        // Remove the static body element
        this.$body.remove();
        // Generate new body element
        this.$b_container.append('<textarea class="body body-editing-mode" id="body-editing-mode" name="body" minlength="5" maxlength="30000" required></textarea>');
        // Now append the saved text to it
        console.log(this.saved_body_text);
        $(".body-editing-mode").html(this.saved_body_text);
    };
    return PostEditing;
}());
var editing = new PostEditing();
editing.swapBodyWithTextbox();
