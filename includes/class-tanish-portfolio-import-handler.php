<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Import_Handler {

    public function __construct() {
        add_action('wp_ajax_import_project', array($this, 'import_project'));
        add_action('wp_ajax_nopriv_import_project', array($this, 'import_project'));
    }

    public function import_project() {
        // Verify nonce
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'tanish_ajax_nonce')) {
            wp_send_json_error('Security check failed.');
        }

        // Validate required fields
        if (empty($_POST['title'])) {
            wp_send_json_error('Title is required.', 400);
        }

        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);
        $categories = sanitize_text_field($_POST['category']); // Comma-separated categories
        $tags = sanitize_text_field($_POST['tags']); // Comma-separated tags
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);

        // Insert project post
        $post_id = wp_insert_post(array(
            'post_title'   => $title,
            'post_content' => $description,
            'post_status'  => 'publish',
            'post_type'    => 'project'
        ));

        if (!$post_id) {
            wp_send_json_error('Failed to insert project.', 500);
        }

        // Assign multiple categories
        if (!empty($categories)) {
            $category_array = array_map('trim', explode(',', $categories));
            $category_ids = array();

            foreach ($category_array as $category_name) {
                $term = term_exists($category_name, 'category');

                if (!$term) {
                    // Create new category if it doesn't exist
                    $new_term = wp_insert_term($category_name, 'category');
                    if (!is_wp_error($new_term)) {
                        $category_ids[] = $new_term['term_id'];
                    }
                } else {
                    $category_ids[] = $term['term_id'];
                }
            }

            wp_set_post_terms($post_id, $category_ids, 'category');
        }

        // Assign multiple tags
        if (!empty($tags)) {
            $tag_array = array_map('trim', explode(',', $tags));
            wp_set_post_terms($post_id, $tag_array, 'post_tag', false);
        }

        // Save meta fields
        update_post_meta($post_id, '_project_start_date', $start_date);
        update_post_meta($post_id, '_project_end_date', $end_date);

        wp_send_json_success('Project successfully imported with ID: ' . $post_id);
    }
}

