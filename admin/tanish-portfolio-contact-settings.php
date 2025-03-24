<?php 

if(!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Contact_Settings {
    function __construct() {
        add_action('admin_menu', array($this, 'tanish_portfolio_add_contact_menu'));
        add_action('admin_enqueue_scripts', array($this, 'tanish_portfolio_admin_scripts'));
        add_action('wp_ajax_tanish_save_contact_settings', array($this, 'tanish_portfolio_save_contact_settings'));
    }

    function tanish_portfolio_add_contact_menu() {
        add_submenu_page( 
            'edit.php?post_type=project', 
            'Contact Us Email Settings', 
            'Contact Us Email', 
            'manage_options', 
            'tanish-portfolio-contact', 
            array($this, 'tanish_portfolio_contact_settings_page')
        );
    }

    function tanish_portfolio_admin_scripts($hook) {
        if ($hook !== 'portfolio_page_tanish-portfolio-contact') {
            return;
        }
    
        wp_enqueue_script(
            'tanish-portfolio-contact-settings',
            plugin_dir_url(__FILE__) . 'js/tanish-portfolio-contact-settings.js',
            ['jquery'],
            null,
            true
        );
    
        wp_localize_script('tanish-portfolio-contact-settings', 'tanishAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tanish_contact_nonce')
        ]);
    }

    // Callback function to display the settings page
    function tanish_portfolio_contact_settings_page() {
        ?>
        <div class="wrap">
            <h1>Contact Us Email Settings</h1>

            <form id="tanish-portfolio-contact-settings">
                <h2>Email Send To</h2>
                <p>Enter email(s) where contact form submissions will be sent. (Comma-separated for multiple emails)</p>
                <input type="text" id="tanish_email_send_to" name="tanish_email_send_to" 
                    value="<?php echo esc_attr(get_option('tanish_email_send_to', '')); ?>" 
                    class="regular-text">

                <h2>AutoReply</h2>
                <p>Configure the automatic reply email.</p>

                <table class="form-table">
                    <tr>
                        <th>From Email:</th>
                        <td>
                            <input type="email" id="tanish_autoreply_email" name="tanish_autoreply_email" value="<?php echo esc_attr(get_option('tanish_autoreply_email', get_option('admin_email'))); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th>From Name:</th>
                        <td>
                            <input type="text" id="tanish_autoreply_name" name="tanish_autoreply_name" 
                                value="<?php echo esc_attr(get_option('tanish_autoreply_name', 'Admin')); ?>" 
                                class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Message Body:</th>
                        <td>
                            <?php
                            $autoreply_message = get_option('tanish_autoreply_message', 'Thank you for reaching out. We will get back to you within 24 hours.');
                            wp_editor($autoreply_message, 'tanish_autoreply_message', [
                                'textarea_name' => 'tanish_autoreply_message',
                                'media_buttons' => false,
                                'textarea_rows' => 5,
                                'teeny' => true
                            ]);
                            ?>
                        </td>
                    </tr>
                </table>

                <button type="submit" class="button button-primary">Save Settings</button>
                <p id="tanish-contact-save-status"></p>
            </form>
        </div>
        <?php
    }

    // AJAX Handler for Saving Settings
    function tanish_portfolio_save_contact_settings() {
        if(!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized access']);
        }

        if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tanish_contact_nonce')) {
            wp_send_json_error(['message' => 'Invalid request']);
        }

        update_option('tanish_email_send_to', sanitize_text_field( $_POST['email_send_to']));
        update_option('tanish_autoreply_email', sanitize_email($_POST['autoreply_email']));
        update_option('tanish_autoreply_name', sanitize_text_field($_POST['autoreply_name']));
        update_option('tanish_autoreply_message', wp_kses_post($_POST['autoreply_message']));

        wp_send_json_success(['message' => 'Settings saved successfully']);
    }
}