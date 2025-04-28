$(document).ready(function () {
    // Load the default dashboard content when the page loads
    $("#main-content").load("dashboard_content.php");

    // When a sidebar link is clicked
    $(".content-link").click(function (e) {
        e.preventDefault(); // Prevent page refresh

        var page = $(this).data("page"); // Get page from data-page attribute

        // Remove "active" class from all menu items
        $(".content-link").removeClass("active");

        // Add "active" class to the clicked item
        $(this).addClass("active");

        // Load content with fade effect
        $("#main-content").fadeOut(200, function () {
            $(this).load(page, function () {
                $(this).fadeIn(200); // Smooth transition effect
            });
        });
    });

    // Sidebar Toggle Animation
    $("#toggleSidebar").click(function () {
        $(".sidebar").toggleClass("collapsed");
        $(".main-content").toggleClass("collapsed");
    });
});
