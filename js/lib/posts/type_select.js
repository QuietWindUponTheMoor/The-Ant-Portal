/*
Types:
0 = Question
1 = General
2 = informative
3 = observation
4 = nuptial_flight

*/
// Initialize vars
let tags = [];
let images_obj = [];

const submit_section =
`
<div class="section col">
    <button class="btn-secondary" type="button" id="cancel-post">Cancel Post</button>
    <button class="btn-main" type="submit">Create Post</button>
</div>
`;

const types = {
    0: {
        type: "question",
        html:
        `
        <div class="section col">
            <label for="title">Question Title</label>
            <input type="text" class="input-main" id="title" name="title" required/>
        </div>
        <div class="section col">
            <label for="title">Please give more details about your question.</label>
            <textarea class="input-main" id="body" name="body" required></textarea>
        </div>
        <div class="section col">
            <label for="tags-input">Enter tags, separated by a comma "," [Left-click to remove]</label>
            <div class="section row" id="selected-tags"></div>
            <input type="text" class="input-main" id="tags-input" name="tags-input"/>
        </div>
        <div class="section col">
            <label for="select-images">Select Images (Optional) [Right-click to remove]</label>
            <div class="section col" id="selected-images">
            </div>
            <input class="hidden" type="file" id="image" name="image[]" accept="image/*" multiple/>
            <button class="btn-action" type="button" id="select-images" onclick="$('#image').trigger('click');">Select Images (Optional)</button>
        </div>
        ${submit_section}
        `
    },
    1: {
        type: "general",
        html:
        `
        <div class="section col">
            <label for="title">Post Title</label>
            <input type="text" class="input-main" id="title" name="title" required/>
        </div>
        <div class="section col">
            <label for="title">Please give more details about your post.</label>
            <textarea class="input-main" id="body" name="body" required></textarea>
        </div>
        <div class="section col">
            <label for="tags-input">Enter tags, separated by a comma "," [Left-click to remove]</label>
            <div class="section row" id="selected-tags"></div>
            <input type="text" class="input-main" id="tags-input" name="tags-input"/>
        </div>
        <div class="section col">
            <label for="select-images">Select Images (Optional) [Right-click to remove]</label>
            <div class="section col" id="selected-images">
            </div>
            <input class="hidden" type="file" id="image" name="image[]" accept="image/*" multiple/>
            <button class="btn-action" type="button" id="select-images" onclick="$('#image').trigger('click');">Select Images (Optional)</button>
        </div>
        ${submit_section}
        `
    },
    2: {
        type: "informative",
        html:
        `
        <div class="section col">
            <label for="title">Post Title</label>
            <input type="text" class="input-main" id="title" name="title" required/>
        </div>
        <div class="section col">
            <label for="title">Please give more details about your post.</label>
            <textarea class="input-main" id="body" name="body" required></textarea>
        </div>
        <div class="section col">
            <label for="tags-input">Enter tags, separated by a comma "," [Left-click to remove]</label>
            <div class="section row" id="selected-tags"></div>
            <input type="text" class="input-main" id="tags-input" name="tags-input"/>
        </div>
        <div class="section col">
            <label for="select-images">Select Images (Optional) [Right-click to remove]</label>
            <div class="section col" id="selected-images">
            </div>
            <input class="hidden" type="file" id="image" name="image[]" accept="image/*" multiple/>
            <button class="btn-action" type="button" id="select-images" onclick="$('#image').trigger('click');">Select Images (Optional)</button>
        </div>
        ${submit_section}
        `
    },
    3: {
        type: "observation",
        html:
        `
        observation
        `
    },
    4: {
        type: "nuptial-flight",
        html:
        `
        nuptial flight
        `
    }
}

$(".type-select").on("change", function() {
    // First, remove any previous items from the page, and arrays
    $("#fields-list").empty();
    tags = [];
    images_obj = [];

    // Get the html content of post selected
    const post_html = types[parseInt($(this).val())].html;

    // Append to fields-list
    $("#fields-list").append(post_html);
});