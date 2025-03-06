<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Export_Handler {

    public function __construct() {
        add_action('wp_ajax_export_projects', array($this, 'export_projects'));
        add_action('wp_ajax_nopriv_export_projects', array($this, 'export_projects'));
    }

    public function export_projects() {
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'tanish_ajax_nonce')) {
            wp_send_json_error('Security check failed.', 500);
        }

        $columns = isset($_POST['columns']) ? explode(',', sanitize_text_field($_POST['columns'])) : array();
        $category_filter = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $tag_filter = isset($_POST['tags']) ? sanitize_text_field($_POST['tags']) : '';
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = 5;

        $args = array(
            'post_type'      => 'project',
            'posts_per_page' => $per_page,
            'paged'          => $page
        );

        // Filter by category
        if (!empty($category_filter)) {
            $args['category_name'] = $category_filter;
        }

        // Filter by tag
        if (!empty($tag_filter)) {
            $args['tag'] = $tag_filter;
        }

        $query = new WP_Query($args);
        $projects = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $project_data = array(
                    'Title'       => get_the_title(),
                    'Description' => get_the_content(),
                    'Categories'  => implode(', ', wp_get_post_terms(get_the_ID(), 'category', array('fields' => 'names'))),
                    'Tags'        => implode(', ', wp_get_post_terms(get_the_ID(), 'post_tag', array('fields' => 'names'))),
                    'Start Date'  => get_post_meta(get_the_ID(), '_project_start_date', true),
                    'End Date'    => get_post_meta(get_the_ID(), '_project_end_date', true)
                );

                if (!empty($columns)) {
                    $project_data = array_intersect_key($project_data, array_flip($columns));
                }

                $projects[] = $project_data;
            }
        }

        wp_reset_postdata();

        if (empty($projects)) {
            wp_send_json_error('No more records to export.');
        }

        wp_send_json_success($projects);
    }
}


