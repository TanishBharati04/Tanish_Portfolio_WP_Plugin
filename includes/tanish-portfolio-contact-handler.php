<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Contact_Handler {
    function __construct() {
        add_action('wp_ajax_nopriv_tanish_portfolio_send_contact_email', array($this, 'tanish_portfolio_send_contact_email'));
        add_action('wp_ajax_tanish_portfolio_send_contact_email', array($this, 'tanish_portfolio_send_contact_email'));
    }

    // AJAX Handler for Sending Emails
    function tanish_portfolio_send_contact_email() {
        if (ob_get_length()) ob_end_clean(); // Prevent unexpected output

        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tanish_contact_nonce')) {
            wp_send_json_error(['message' => 'Invalid request.']);
            exit;
        }

        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);

        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error(['message' => 'All fields are required.']);
            exit;
        }

        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Invalid email format.']);
            exit;
        }

        // Get admin email settings
        $admin_emails = get_option('tanish_email_send_to', get_option('admin_email'));
        $admin_email_list = array_map('trim', explode(',', $admin_emails));

        $subject = "New Contact Form Submission from $name";
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $headers = [
            "From: WordPress Contact Form <noreply@yourdomain.com>",
            "Reply-To: $name <$email>",
            "Content-Type: text/plain; charset=UTF-8"
        ];        

        // Send email to admin(s)
        $sent = wp_mail($admin_email_list, $subject, $body, $headers);

        if (!$sent) {
            $error = error_get_last(); // Get last PHP error (if any)
            error_log("Contact Form Error: Failed to send email. PHP Error: " . print_r($error, true));
            wp_send_json_error(['message' => 'Failed to send email. Please try again later.']);
            exit;
        }

        // Get AutoReply settings
        $autoreply_email = get_option('tanish_autoreply_email', get_option('admin_email'));
        $autoreply_name = get_option('tanish_autoreply_name', 'Admin');
        $autoreply_message = get_option('tanish_autoreply_message', 'Thank you for reaching out. We will get back to you within 24 hours.');

        $autoreply_subject = "Thank You for Contacting Us!";
        $autoreply_headers = [
            "From: $autoreply_name <$autoreply_email>",
            "Reply-To: $autoreply_email"
        ];

        // Send AutoReply email to user
        $autoreply_sent = wp_mail($email, $autoreply_subject, $autoreply_message, $autoreply_headers);

        if (!$autoreply_sent) {
            $error = error_get_last();
            error_log("AutoReply Email Error: " . print_r($error, true));
        }

        wp_send_json_success(['message' => 'Message sent successfully!']);
    }
}

