//Dropdown manager
$("#account-trigger").on("click", () => {
    $("#account-dropdown-image").toggleClass("rotate");
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