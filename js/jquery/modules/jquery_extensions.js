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
// Now extend the JQuery object with the new methods
/*$.fn.extend({
    changeAttrOnHover: changeAttrOnHover,
});*/
$.fn.changeAttrOnHover = changeAttrOnHover;
