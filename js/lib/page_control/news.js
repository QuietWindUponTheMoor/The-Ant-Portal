// If collapse button is clicked
$("#news-collapse").click("click", () => {
    // Toggle slideup/slidedown
    $("#news-bottom").slideToggle();

    // Check whether slid up or slid down, and change button
    if ($("#news-collapse").text() === "-") {
        $("#news-collapse").text("+");
    } else if ($("#news-collapse").text() === "+") {
        $("#news-collapse").text("-")
    }
});


