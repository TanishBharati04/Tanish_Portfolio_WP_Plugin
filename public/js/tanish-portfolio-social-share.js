jQuery(document).ready(function ($) {
    $(".instagram-share-btn").on("click", function () {
        var button = $(this);
        var projectId = button.data("project-id");
        var projectUrl = button.data("project-url");
        var projectTitle = button.data("project-title");

        // Fake Instagram share logic (since Instagram doesn’t allow direct sharing)
        alert("To share, copy this link: " + projectUrl);

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
            }
        });
    });
});
