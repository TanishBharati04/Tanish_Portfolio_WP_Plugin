<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Import_Handler {
    
    public function __construct() {
        add_action('wp_ajax_tanish_import_csv', array($this, 'handle_csv_upload'));
        add_action('wp_ajax_nopriv_tanish_import_csv', array($this, 'handle_csv_upload'));

        // Register AJAX action for batch processing
        add_action('wp_ajax_tanish_process_csv_batch', array($this, 'process_csv_batch'));
    }

    // Handle CSV Upload
    public function handle_csv_upload() {
        check_ajax_referer('tanish_nonce', 'security');

        // if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'tanish_nonce')) {
        //     error_log("Security check failed in import handler handle_csv_upload.");
        //     wp_send_json_error(array('message' => 'Security check failed in import handler handle_csv_upload.'));
        // }
    
        if (!isset($_FILES['csv_file'])) {
            wp_send_json_error(array('message' => 'No file uploaded.'));
        }
    
        $file = $_FILES['csv_file'];
    
        // Validate file format
        $allowed_mime_types = array('text/csv', 'application/vnd.ms-excel');
        if (!in_array($file['type'], $allowed_mime_types)) {
            wp_send_json_error(array('message' => 'Invalid file format. Please upload a CSV file.'));
        }
    
        $csv_file = fopen($file['tmp_name'], 'r');
        if (!$csv_file) {
            wp_send_json_error(array('message' => 'Failed to open CSV file.'));
        }
    
        // Read header row
        $header = fgetcsv($csv_file);
        if (!$header) {
            wp_send_json_error(array('message' => 'Empty CSV file.'));
        }
    
        $required_columns = array('title', 'description', 'category', 'tag', 'start_date', 'end_date');
        
        // Validate columns
        foreach ($required_columns as $col) {
            if (!in_array($col, $header)) {
                wp_send_json_error(array('message' => "Missing required column: $col"));
            }
        }
    
        // Store rows in session for batch processing
        $csv_data = [];
        while (($row = fgetcsv($csv_file)) !== false) {
            $csv_data[] = array_combine($header, $row);
        }
        fclose($csv_file);
    
        // Store data in a transient for batch processing
        $batch_size = 5;
        set_transient('tanish_import_csv_data', $csv_data, HOUR_IN_SECONDS);
        set_transient('tanish_import_csv_progress', 0, HOUR_IN_SECONDS);
        
        wp_send_json_success(array(
            'message' => 'CSV file validated successfully.',
            'total_records' => count($csv_data),
            'batch_size' => $batch_size
        ));
    }

    public function process_csv_batch() {
        check_ajax_referer('tanish_nonce', 'security');
    
        $csv_data = get_transient('tanish_import_csv_data');
        if (!$csv_data || empty($csv_data)) {
            wp_send_json_success(array('message' => 'Import completed.', 'completed' => true));
        }
    
        $batch_size = 5;
        $processed = 0;
    
        for ($i = 0; $i < $batch_size; $i++) {
            if (empty($csv_data)) {
                break;
            }
    
            $record = array_shift($csv_data);
            
            // Insert project post
            $post_id = wp_insert_post(array(
                'post_title'   => sanitize_text_field($record['title']),
                'post_content' => sanitize_textarea_field($record['description']),
                'post_status'  => 'publish',
                'post_type'    => 'project'
            ));
    
            if ($post_id) {
                // Assign category
                wp_set_object_terms($post_id, sanitize_text_field($record['category']), 'project_category');
                // Assign tags
                wp_set_object_terms($post_id, explode(',', sanitize_text_field($record['tag'])), 'project_tag');
                // Add metadata
                update_post_meta($post_id, 'start_date', sanitize_text_field($record['start_date']));
                update_post_meta($post_id, 'end_date', sanitize_text_field($record['end_date']));
            }
    
            $processed++;
        }
    
        set_transient('tanish_import_csv_data', $csv_data, HOUR_IN_SECONDS);
        $progress = get_transient('tanish_import_csv_progress') + $processed;
        set_transient('tanish_import_csv_progress', $progress, HOUR_IN_SECONDS);
    
        wp_send_json_success(array(
            'message' => "$progress records uploaded...",
            'progress' => $progress,
            'completed' => empty($csv_data)
        ));
    }   
    
}

