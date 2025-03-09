<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Export_Handler {

    public function __construct() {
        add_action('wp_ajax_export_projects', array($this, 'export_projects'));
        add_action('wp_ajax_nopriv_export_projects', array($this, 'export_projects'));

        // Register AJAX action for fetching taxonomies
        add_action('wp_ajax_tanish_fetch_taxonomies', array($this, 'fetch_taxonomies'));
        add_action('wp_ajax_nopriv_tanish_fetch_taxonomies', array($this, 'fetch_taxonomies'));
    }

    // Handle fetching categories and tags for export section
    public function fetch_taxonomies() {
        // Debug received nonce
        error_log("Received Nonce in export handler: " . ($_POST['security'] ?? 'NOT SET'));
        
        // Fix: Use check_ajax_referer instead of wp_verify_nonce
        check_ajax_referer('tanish_nonce', 'security');

        // Above method and this below method to check nonce, both are correct, but since check_ajax_referer is simpler and recommended by wordpress we use this. 
        // if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'tanish_nonce')) {
        //     error_log("Security check failed in fetch_taxonomies.");
        //     wp_send_json_error(array('message' => 'Security check failed.'));
        //     return;
        // }

        error_log("Fetching Taxonomies... in export handler");

        // Fetch categories
        $categories = get_terms(array(
            'taxonomy'   => 'category',
            'hide_empty' => false,
        ));

        // Fetch tags
        $tags = get_terms(array(
            'taxonomy'   => 'post_tag',
            'hide_empty' => false,
        ));

        if (is_wp_error($categories)) {
            error_log("Error fetching categories: " . $categories->get_error_message());
            wp_send_json_error(array('message' => 'Failed to fetch categories.'));
        }
    
        if (is_wp_error($tags)) {
            error_log("Error fetching tags: " . $tags->get_error_message());
            wp_send_json_error(array('message' => 'Failed to fetch tags.'));
        }

        $category_list = array();
        $tag_list = array();

        foreach ($categories as $category) {
            $category_list[] = array(
                'id'   => $category->term_id,
                'name' => $category->name
            );
        }

        foreach ($tags as $tag) {
            $tag_list[] = array(
                'id'   => $tag->term_id,
                'name' => $tag->name
            );
        }

        error_log("Categories and Tags Fetched Successfully.");

        wp_send_json_success(array(
            'categories' => $category_list,
            'tags'       => $tag_list
        ));
    }

    public function export_projects() {
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'tanish_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed in export handler export_projects.'));
        }

        $columns = isset($_POST['columns']) ? explode(',', sanitize_text_field($_POST['columns'])) : array();
        $category_filter = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $tag_filter = isset($_POST['tags']) ? sanitize_text_field($_POST['tags']) : '';

        $args = array(
            'post_type'      => 'project',
            'posts_per_page' => -1, // Get all records
            'post_status'    => 'publish'
        );

        // Filtering by category and tags using tax_query
        $tax_query = array('relation' => 'AND');

        if (!empty($category_filter) && $category_filter !== 'all') {
            $tax_query[] = array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $category_filter
            );
        }

        if (!empty($tag_filter) && $tag_filter !== 'all') {
            $tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'slug',
                'terms'    => $tag_filter
            );
        }

        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
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

                if (!empty($columns) && $columns[0] !== 'all') {
                    $project_data = array_intersect_key($project_data, array_flip($columns));
                }

                $projects[] = $project_data;
            }
        }

        wp_reset_postdata();

        if (empty($projects)) {
            wp_send_json_error(array('message' => 'No records found.'));
        }

        // Generate CSV File
        $csv_file_path = $this->generate_csv_file($projects);
        if ($csv_file_path) {
            wp_send_json_success(array('file_url' => $csv_file_path));
        } else {
            wp_send_json_error(array('message' => 'Failed to generate CSV.'));
        }
    }

    private function generate_csv_file($projects) {
        $upload_dir = wp_upload_dir();
        $csv_filename = 'exported_projects_' . time() . '.csv';
        $csv_filepath = $upload_dir['path'] . '/' . $csv_filename;

        $file = fopen($csv_filepath, 'w');
        if (!$file) {
            wp_send_json_error(['message' => 'Failed to create CSV file.']);
            return;
        }

        // Set CSV headers
        fputcsv($file, array_keys($projects[0]));

        // Insert data
        foreach ($projects as $project) {
            fputcsv($file, $project);
        }

        fclose($file);

        return $upload_dir['url'] . '/' . $csv_filename;
    }
}
