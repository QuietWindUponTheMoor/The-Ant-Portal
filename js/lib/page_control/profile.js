// Check if profile top is visible
let profile_top = document.querySelector(".profile-top");

// On scroll
window.onscroll = (e) => {
    
}






// Helper functions
function outOfView(el) {
    let rect = el.getBoundingClientRect();
    let elTop = rect.top;
    let elBottom = rect.bottom;
    // Check if visible
    let outOfView = (elBottom < 99);
    console.log(elTop, elBottom);
    return outOfView;
}