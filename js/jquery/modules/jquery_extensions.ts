/*interface JQuery {
    changeAttrOnHover(attrType: string, attrValOnHover: string, attrValWithoutHover: string): JQuery;
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
// Now extend the JQuery object with the new methods
$.fn.extend({
    changeAttrOnHover: changeAttrOnHover,
});

*/