// Declarations
declare const user_data: {
    id: number;
    username: string;
};
declare const post_data: {
    db: string;
    postType: number;
    postID: number;
};

// Initializations
let globalPostID: number;
let globalSpecies: string = "";
let globalTitle: JQuery<HTMLElement>;
let globalBody: JQuery<HTMLElement>;

// Imports
interface JQuery {
    changeAttrOnHover(attrType: string, attrValOnHover: string, attrValWithoutHover: string): JQuery;
    renderLineBreak(): JQuery;
    convertToEditable(): JQuery;
    submitWithAJAX(url: string, method: string): any;
    getNodeType(): string | undefined;
    onSuccess(callback: (response: any) => boolean): this;
}
// Methods to extend
function changeAttrOnHover(attrType: string, attrValOnHover: string, attrValWithoutHover: string): JQuery {
    this.on("mouseenter", function () {
        $(this).attr(attrType, attrValOnHover);
    });
    this.on("mouseleave", function () {
        $(this).attr(attrType, attrValWithoutHover);
    });

    return this;
}
function renderLineBreak(): JQuery {
    return this.html().replace(/<br\s*[/]?>/gi, "\n");
}
function convertToEditable(): JQuery {
    // Initializations
    let saved_text: string = "";
    let container: JQuery<HTMLElement>;
    let element: JQuery<HTMLElement>;
    let typeObject: any;

    // Get the ID of the element
    const ID: string = this.attr("id");
    // Save the element's contents
    saved_text = this.renderLineBreak();

    // Get append type
    interface Appends {
        [key: string]: {
            element: JQuery<HTMLElement>;
            label: string,
            elementID: string,
            container: JQuery<HTMLElement>;
            attributes: {name: string, value: string}[];
        };
    }
    const appends: Appends = {
        title: {
            element: $("<input />") as JQuery<HTMLElement>,
            label: '<label for="title-editing-mode">Suggest an edit to the post\'s title</label>',
            elementID: "title-editing-mode",
            container: $("#title-container"),
            attributes: [
                {name: "class", value: "text-editing-mode"},
                {name: "minlength", value: "5"},
                {name: "maxlength", value: "256"},
                {name: "required", value: "true"},
            ]
        },
        body: {
            element: $("<textarea></textarea>") as JQuery<HTMLElement>,
            label: '<label for="title-editing-mode">Suggest an edit to the post\'s body</label>',
            elementID: "body-editing-mode",
            container: $(".body-container"),
            attributes: [
                {name: "class", value: "body"},
                {name: "class", value: "text-editing-mode"},
                {name: "minlength", value: "5"},
                {name: "maxlength", value: "30000"},
                {name: "required", value: "true"},
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
            const regex: RegExp = /^.*?#(\d+):\s*([^:]+)$/;
            let match: object = saved_text.match(regex);
            let species: string = match[2];
            let postID: number = parseInt(match[1]);
            saved_text = species;
            // Now assign global variabls 'postID' and 'species' and 'title':
            globalPostID = postID;
            globalSpecies = species;
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
            console.error(`Invalid element on ID '${ID}'`);
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
    typeObject.attributes.forEach(attr => {
        if (attr.name === "class") {
            element.addClass(attr.value);
        } else {
            element.attr(attr.name, attr.value);
        }
    });

    // Now assign it the proper value
    element.val(saved_text);

    // Return item
    return this; // move this to onSuccess() later
}
function submitWithAJAX(url: string, method: string): JQuery {
    /*
    use case of this function:
    $("#test").submitWithAJAX("url", "test", "POST", false, false).onSuccess(response => {
        console.log(response);
    });
    */

    // Get element type
    const elementType: string = this.getNodeType();
    this.on("click", () => {
        if (elementType === "button") {
            // Process data
            let newTitle: string | any = globalTitle.val();
            let newBody: string | any = globalBody.val();
            newTitle = `Nuptial Flight #${globalPostID}: ${globalSpecies}`;
            newBody = newBody.replace(/\n/g, "<br>");
            let byUserID: number = user_data.id;
            let postType: number = post_data.postType;
            let postID: number = post_data.postType;
            let db: string = post_data.db;
        
            let dataObj: object = {
                db: db,
                userID: byUserID,
                postType: postType,
                postID: postID,
                newTitle: newTitle,
                newBody: newBody,
            }

            // Execute AJAX
            $.ajax({  
                type: method,  
                url: url, 
                data: dataObj,
                success: (response) => {
                    if (this.data("onSuccessCallback")) {
                        this.data("onSuccessCallback")(response);
                    }
                },
            });
        } else {
            console.error("The element: ", this[0], "is not the correct type of element to call submitWithAJAX on. submitWithAJAX must be called on a JQuery object of type 'BUTTON'.");
        }
    });

    return this;
}
function getNodeType(): string | undefined {
    return this.prop("nodeName").toLowerCase();
}

// Now extend the JQuery object with the new methods
$.fn.extend({
    changeAttrOnHover: changeAttrOnHover,
    renderLineBreak: renderLineBreak,
    convertToEditable: convertToEditable,
    submitWithAJAX: submitWithAJAX,
    onSuccess: function(callback) {
        this.data("onSuccessCallback", callback);
        return this;
    },
    getNodeType: getNodeType
});

$("#start-editing").on("click", () => {
    // Static vars
    const controls_container: JQuery<HTMLElement> = $("#control-buttons");

    const elementsToConvert: Array<JQuery<HTMLElement>> = [
        $("#title"),
        $(".body"),
    ];

    // Convert body
    elementsToConvert.forEach(element => {
        element.convertToEditable();
    });

    const elementsToRemove: Array<JQuery<HTMLElement>> = [
        $(".post-flagging"),
        $(".vote-container"),
        $("#user-data-section"),
        $(".images-section"),
        $(".post-meta"),
        $("#start-editing-container"),
        $(".post-page-comments")
    ];

    // Remove needless elements
    elementsToRemove.forEach(element => {
        element.hide();
    });

    // Lastly, show the confirmation/cancel buttons
    $("#cancel-edits").css("display", "flex");
    $("#finish-edits").css("display", "flex");

    $("#cancel-edits").on("click", () => {
        window.location.reload();
    });
});

$("#finish-edits").submitWithAJAX("/php/lib/posts/edit_suggestions.php", "POST").onSuccess(response => {
    // Handle response here
});
    
// Extras
$(".control-button").changeAttrOnHover("src", "/web_images/icons/editing_active.png", "/web_images/icons/editing.png");