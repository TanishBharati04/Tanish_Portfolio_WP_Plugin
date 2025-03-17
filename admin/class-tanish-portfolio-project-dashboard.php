<?php 

if(!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Project_Dashboard {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=project', // Parent slug: "Projects"
            'Analytics Dashboard', // Page Title
            'Analytics Dashboard', // Menu Title
            'manage_options', // Capability
            'tanish-portfolio-analytics', // Menu Slug
            array($this, 'display_dashboard')
        );
    }

    public function display_dashboard() {
        include plugin_dir_path( __FILE__ ) . 'partials/tanish-portfolio-analytics.php';
    }
}