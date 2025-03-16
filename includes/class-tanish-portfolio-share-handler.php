<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Share_Handler {
    public function __construct() {
        add_action('wp_ajax_tanish_portfolio_increase_share_count', array($this, 'increase_share_count'));
        add_action('wp_ajax_nopriv_tanish_portfolio_increase_share_count', array($this, 'increase_share_count'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_share_assets'));
    }

    public function enqueue_share_assets() {
        wp_enqueue_style('tanish-portfolio-share-css', plugin_dir_url(__FILE__) . '../public/css/tanish-portfolio-social-share.css');
    
        wp_enqueue_script('tanish-portfolio-share-js', plugin_dir_url(__FILE__) . '../public/js/tanish-portfolio-social-share.js', array('jquery'), null, true);

        wp_localize_script( 'tanish-portfolio-share-js', 'tanishAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tanish_nonce'),
        ]);
    }
    

    public function increase_share_count() {
        // Verify nonce for security
        check_ajax_referer('tanish_nonce', 'nonce');

        if (!isset($_POST['project_id'])) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'tanish_project_shares';

        $project_id = intval($_POST['project_id']);
        $user_id = get_current_user_id();
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Check if project is already in the table
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table_name} WHERE project_id = %d",
            $project_id
        ));

        if ($existing) {
            // Update existing share count
            $wpdb->query($wpdb->prepare(
                "UPDATE {$table_name} SET share_count = share_count + 1 WHERE project_id = %d",
                $project_id
            ));
        } else {
            // Insert new record
            $wpdb->insert($table_name, [
                'project_id' => $project_id,
                'user_id' => $user_id,
                'ip_address' => $ip_address,
                'share_count' => 1,
                'timestamp' => current_time('mysql')
            ]);
        }

        // Get updated share count
        $share_count = $wpdb->get_var($wpdb->prepare(
            "SELECT share_count FROM {$table_name} WHERE project_id = %d",
            $project_id
        ));

        wp_send_json_success(array('share_count' => $share_count));
    }
}

