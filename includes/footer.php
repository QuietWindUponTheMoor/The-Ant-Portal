<script type="text/javascript">
$(".container").css("padding-top", getNavbarHeight() + "px");
$(window).on("resize", function() {
    $(".container").css("padding-top", getNavbarHeight() + "px");
});

function getNavbarHeight() {
    return $(".nav").height() + 25;
}
</script>