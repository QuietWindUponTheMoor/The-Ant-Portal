class PostEditing {
    // Static
    private $b_container: any = $(".body-container");
    private $body: any = $(".body");
    private $editing_body: any = $(".body-editing-mode");

    // Dynamic
    private saved_body_text: string;

    public constructor() {

    }

    public swapBodyWithTextbox(): void {
        // Get the text of the body & save it
        this.saved_body_text = this.$body.text();

        // Remove the static body element
        this.$body.remove();

        // Generate new body element
        this.$b_container.append('<textarea class="body body-editing-mode" id="body-editing-mode" name="body" minlength="5" maxlength="30000" required></textarea>');
        // Now append the saved text to it
        console.log(this.saved_body_text);
        $(".body-editing-mode").html(this.saved_body_text);
    }
}


const editing = new PostEditing();
editing.swapBodyWithTextbox();