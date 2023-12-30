// Dropdown manager
//$("#account-dropdown-image").css("transform", "rotate(180deg)");
$("#account-trigger").on("click", () => {
    $("#account-dropdown-image").toggleClass("rotate");
    $("#account-dropdown").slideToggle(200); // 0.2s / 200ms (The slide animation is ever so slightly longer than the rotate is despite both being 0.3s)
});

// Account name color
if ($("#account-name").length > 0) {
    const acct_colors = [
        "cyan",
        "purple",
        "pink",
        "red",
        "green",
        "limegreen",
        "yellow",
        "gold",
        "teal",
        "blue",
        "darkblue"
    ];

    // Generate random number
    const color_length = acct_colors.length;
    const color = acct_colors[Math.floor(Math.random() * color_length)];
    $("#account-name").css("color", color);
}