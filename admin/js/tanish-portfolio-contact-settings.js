jquery(document).ready(function($) {
    $('#tanish-portfolio-contact-settings').on('submit', function(e) {
        e.preventDefault();

        let data = {
            action : 'tanish_save_contact_settings',
            nonce : tanishAjax.nonce,
            email_send_to : $('#tanish_email_send_to').val(),
            autoreply_email :  $("#tanish_autoreply_email").val(),
            autoreply_name : $("#tanish_autoreply_name").val(),
            autoreply_message : tinyMCE.get("tanish_autoreply_message").getContent()
        };

        $.post(tanishAjax.ajaxurl, data, function(response) {
            if(response.success) {
                // console.log(response.data.message);
                $("#tanish-contact-save-status").text(response.data.message).css("color", "green");
            } else {
                // console.log(response.data.message);
                $("#tanish-contact-save-status").text(response.data.message).css("color", "red");
            }
        });
    });
});