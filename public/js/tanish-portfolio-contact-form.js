jQuery(document).ready(function ($) {
    $("#tanish-portfolio-contact-form").on("submit", function (e) {
        e.preventDefault();

        let name = $("#tanish_contact_name").val().trim();
        let email = $("#tanish_contact_email").val().trim();
        let message = $("#tanish_contact_message").val().trim();
        let statusBox = $("#tanish-contact-form-status");

        if (!name || !email || !message) {
            statusBox.text("All fields are required!").css("color", "red");
            return;
        }

        if (!validateEmail(email)) {
            statusBox.text("Invalid email format!").css("color", "red");
            return;
        }

        let data = {
            action: "tanish_portfolio_send_contact_email",
            nonce: tanishAjax.nonce,
            name: name,
            email: email,
            message: message
        };

        statusBox.text("Sending...").css("color", "blue");

        $.post(tanishAjax.ajaxurl, data, function (response) {
            if (response.success) {
                // console.log(response.data.message);
                statusBox.text(response.data.message).css("color", "green");
                $("#tanish-portfolio-contact-form")[0].reset();
            } else {
                // console.log(response.data.message);
                statusBox.text(response.data.message).css("color", "red");
            }
        }).fail(function () {
            statusBox.text("Something went wrong. Please try again later.").css("color", "red");
        });
    });
});

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}