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
    }
    

    public function increase_share_count() {
        // Verify nonce for security
        check_ajax_referer('tanish_nonce', 'nonce');

        if (!isset($_POST['project_id'])) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }

        $project_id = intval($_POST['project_id']);
        $share_count = get_post_meta($project_id, 'instagram_share_count', true);
        $share_count = ($share_count) ? $share_count + 1 : 1;

        update_post_meta($project_id, 'instagram_share_count', $share_count);

        wp_send_json_success(array('share_count' => $share_count));
    }
}

