jQuery(document).ready(function ($) {
    $(".instagram-share-btn").on("click", function () {
        let button = $(this);
        let projectId = button.data("project-id");
        let projectUrl = button.data("project-url");

        // Open Instagram Share (Placeholder URL, Needs API later)
        // window.open("https://www.instagram.com/share?url=" + encodeURIComponent(projectUrl), "_blank");

        // Send AJAX request to increase share count
        $.ajax({
            url: tanishAjax.ajaxurl,
            type: "POST",
            data: {
                action: "tanish_portfolio_increase_share_count",
                project_id: projectId,
                nonce: tanishAjax.nonce
            },
            success: function (response) {
                if (response.success) {
                    $("#share-count-" + projectId).text(response.data.share_count);
                }
                else {
                    console.error("Share count update failed.");
                }
            }
        });
    });
});
