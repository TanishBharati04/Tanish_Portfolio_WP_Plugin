<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Meta_Box {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_project_meta_box'));
        add_action('save_post', array($this, 'save_project_meta_box'));
    }

    public function add_project_meta_box() {
        add_meta_box(
            'project_details',
            __('Project Details', 'tanish-portfolio'),
            array($this, 'render_project_meta_box'),
            'project',
            'normal',
            'high'
        );
    }

    public function render_project_meta_box($post) {
        // Retrieve existing values
        $start_date = get_post_meta($post->ID, '_project_start_date', true);
        $end_date = get_post_meta($post->ID, '_project_end_date', true);

        // Security nonce
        wp_nonce_field('save_project_details', 'project_details_nonce');

        ?>
        <style>
            .project-details-card {
                background:rgb(255, 250, 250);
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
                max-width: 100%;
            }
            .project-details-card label {
                font-weight: bold;
                display: block;
                margin-top: 10px;
            }
            .project-details-card input {
                width: 100%;
                padding: 8px;
                margin-top: 5px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
        </style>

        <div class="project-details-card">
            <label for="project_start_date"><?php _e('Project Start Date:', 'tanish-portfolio'); ?></label>
            <input type="date" id="project_start_date" name="project_start_date" value="<?php echo esc_attr($start_date); ?>">

            <label for="project_end_date"><?php _e('Project End Date:', 'tanish-portfolio'); ?></label>
            <input type="date" id="project_end_date" name="project_end_date" value="<?php echo esc_attr($end_date); ?>">
        </div>
        <?php
    }

    function save_project_meta_box($post_id) {
        // Check if nonce is set
        if (!isset($_POST['project_details_nonce']) || !wp_verify_nonce($_POST['project_details_nonce'], 'save_project_details')) {
            return;
        }
    
        // Prevent autosave from interfering
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        // Ensure user has permission to edit
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    
        // Debugging: Log the incoming POST data (check error_log)
        error_log('Saving meta box data: ' . print_r($_POST, true));
    
        // Validate and sanitize the start date
        if (isset($_POST['project_start_date'])) {
            $start_date = sanitize_text_field($_POST['project_start_date']);
            update_post_meta($post_id, '_project_start_date', $start_date);
        }
    
        // Validate and sanitize the end date
        if (isset($_POST['project_end_date'])) {
            $end_date = sanitize_text_field($_POST['project_end_date']);
            update_post_meta($post_id, '_project_end_date', $end_date);
        }
    }
    
}
