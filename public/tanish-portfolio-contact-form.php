<?php 

if(!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Contact_Form {
    function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'tanish_portfolio_enqueue_scripts'));
    }

    function tanish_portfolio_enqueue_scripts() {
        if(is_front_page()) {
            wp_enqueue_script('tanish-portfolio-contact-form', 
                plugin_dir_url(__FILE__) . 'js/tanish-portfolio-contact-form.js', 
                ['jquery'], null, true);
    
            wp_localize_script('tanish-portfolio-contact-form', 'tanishAjax', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('tanish_contact_nonce')
            ]);
        }
    }
    
}