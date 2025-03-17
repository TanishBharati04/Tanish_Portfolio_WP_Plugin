<?php

if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Analytics {
    public function __construct() {
        add_action('wp_ajax_tanish_portfolio_get_profile_visits', array($this, 'get_profile_visits'));
    }

    public function get_profile_visits() {
        global $wpdb;

        $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : 'month';

        if ($filter === 'today') {
            $where = "WHERE DATE(timestamp) = CURDATE()";
        } elseif ($filter === 'week') {
            $where = "WHERE YEARWEEK(timestamp, 1) = YEARWEEK(CURDATE(), 1)";
        } else {
            $where = "WHERE MONTH(timestamp) = MONTH(CURDATE()) AND YEAR(timestamp) = YEAR(CURDATE())";
        }

        $total_visits = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tanish_profile_visits $where");

        wp_send_json_success(array('total_visits' => $total_visits));
    }
}


