// Initializations
var globalPostID;
var globalTitle;
var globalBody;
// Methods to extend
function changeAttrOnHover(attrType, attrValOnHover, attrValWithoutHover) {
    this.on("mouseenter", function () {
        $(this).attr(attrType, attrValOnHover);
    });
    this.on("mouseleave", function () {
        $(this).attr(attrType, attrValWithoutHover);
    });
    return this;
}
function renderLineBreak() {
    return this.html().replace(/<br\s*[/]?>/gi, "\n");
}
function convertToEditable() {
    // Initializations
    var saved_text = "";
    var container;
    var element;
    var typeObject;
    // Get the ID of the element
    var ID = this.attr("id");
    // Save the element's contents
    saved_text = this.renderLineBreak();
    var appends = {
        title: {
            element: $("<input />"),
            label: '<label for="title-editing-mode">Suggest an edit to the post\'s title</label>',
            elementID: "title-editing-mode",
            container: $("#title-container"),
            attributes: [
                { name: "class", value: "text-editing-mode" },
                { name: "minlength", value: "5" },
                { name: "maxlength", value: "256" },
                { name: "required", value: "true" },
            ]
        },
        body: {
            element: $("<textarea></textarea>"),
            label: '<label for="title-editing-mode">Suggest an edit to the post\'s body</label>',
            elementID: "body-editing-mode",
            container: $(".body-container"),
            attributes: [
                { name: "class", value: "body" },
                { name: "class", value: "text-editing-mode" },
                { name: "minlength", value: "5" },
                { name: "maxlength", value: "30000" },
                { name: "required", value: "true" },
            ]
        },
    };
    switch (ID) {
        case "title":
            // Assign the appends item as new variable
            typeObject = appends.title;
            // Assign the element
            element = typeObject.element;
            element.attr("id", typeObject.id);
            // Get the element's container
            container = typeObject.container;
            // Do regex for title to get the number (post ID) & the species name
            var regex = /^.*?#(\d+):\s*([^:]+)$/;
            var match = saved_text.match(regex);
            var species = match[2];
            var postID = parseInt(match[1]);
            saved_text = species;
            // Now assign global variabls 'postID' and 'species' and 'title':
            globalPostID = postID;
            globalTitle = element;
            break;
        case "body":
            // Assign the appends item as new variable
            typeObject = appends.body;
            // Assign the element
            element = typeObject.element;
            element.attr("id", typeObject.id);
            // Get the element's container
            container = typeObject.container;
            // Assign global body
            globalBody = element;
            break;
        default:
            console.error("Invalid element on ID '".concat(ID, "'"));
            break;
    }
    // Now remove the element
    this.remove();
    // Change the container's class
    container.addClass("section-wrap");
    // Append the element & assign it the proper attributes
    // Append the label first
    container.append(typeObject.label);
    container.append(element);
    typeObject.attributes.forEach(function (attr) {
        if (attr.name === "class") {
            element.addClass(attr.value);
        }
        else {
            element.attr(attr.name, attr.value);
        }
    });
    // Now assign it the proper value
    element.val(saved_text);
    // Return item
    return this; // move this to onSuccess() later
}
function submitSuggestion(url, method) {
    /*
    use case of this function:
    $("#test").submitSuggestion("url", "test", "POST", false, false).onSuccess(response => {
        console.log(response);
    });
    */
    var _this = this;
    // Get element type
    var elementType = this.getNodeType();
    this.on("click", function () {
        if (elementType === "button") {
            // Process data
            var newTitle = globalTitle.val();
            var newBody = globalBody.val();
            newTitle = "Nuptial Flight #".concat(globalPostID, ": ").concat(newTitle);
            newBody = newBody.replace(/\n/g, "<br>");
            var byUserID = user_data.id;
            var postType = post_data.postType;
            var postID = post_data.postID;
            var db = post_data.db;
            var dataObj = {
                db: db,
                userID: byUserID,
                postType: postType,
                postID: postID,
                newTitle: newTitle,
                newBody: newBody,
            };
            // Execute AJAX
            $.ajax({
                type: method,
                url: url,
                data: dataObj,
                success: function (response) {
                    if (_this.data("onSuccessCallback")) {
                        _this.data("onSuccessCallback")(response);
                    }
                },
            });
        }
        else {
            console.error("The element: ", _this[0], "is not the correct type of element to call submitSuggestion on. submitSuggestion must be called on a JQuery object of type 'BUTTON'.");
        }
    });
    return this;
}
function getNodeType() {
    return this.prop("nodeName").toLowerCase();
}
// Now extend the JQuery object with the new methods
$.fn.extend({
    changeAttrOnHover: changeAttrOnHover,
    renderLineBreak: renderLineBreak,
    convertToEditable: convertToEditable,
    submitSuggestion: submitSuggestion,
    onSuccess: function (callback) {
        this.data("onSuccessCallback", callback);
        return this;
    },
    getNodeType: getNodeType
});
$("#start-editing").on("click", function () {
    // Static vars
    var controls_container = $("#control-buttons");
    var elementsToConvert = [
        $("#title"),
        $(".body"),
    ];
    // Convert body
    elementsToConvert.forEach(function (element) {
        element.convertToEditable();
    });
    var elementsToRemove = [
        $(".post-flagging"),
        $(".vote-container"),
        $("#user-data-section"),
        $(".images-section"),
        $(".post-meta"),
        $("#start-editing-container"),
        $(".post-page-comments")
    ];
    // Remove needless elements
    elementsToRemove.forEach(function (element) {
        element.hide();
    });
    // Lastly, show the confirmation/cancel buttons
    $("#cancel-edits").css("display", "flex");
    $("#finish-edits").css("display", "flex");
    $("#cancel-edits").on("click", function () {
        window.location.reload();
    });
});
$("#finish-edits").submitSuggestion("/php/lib/posts/edit_suggestions.php", "POST").onSuccess(function (response) {
    // Handle response here
    if (parseInt(response) === 1) {
        // Success
        window.location.reload();
    }
    else {
        // Error
        console.error(response);
    }
});
// Extras
$(".control-button").changeAttrOnHover("src", "/web_images/icons/editing_active.png", "/web_images/icons/editing.png");
