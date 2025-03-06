<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Import_Export {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_import_export_submenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    // Add Submenu under "Projects"
    public function add_import_export_submenu() {
        add_submenu_page(
            'edit.php?post_type=project',
            __('Import/Export Projects', 'tanish-portfolio'),
            __('Import/Export Projects', 'tanish-portfolio'),
            'manage_options',
            'import-export-projects',
            array($this, 'render_import_export_page')
        );
    }

    // Enqueue scripts and styles
    public function enqueue_scripts() {
        wp_enqueue_style('tanish-portfolio-style', plugins_url('../assets/import-export.css', __FILE__));
        wp_enqueue_script('tanish-portfolio-script', plugins_url('../assets/import-export.js', __FILE__), array('jquery'), null, true);
        wp_localize_script('tanish-portfolio-script', 'tanishAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tanish_nonce')
        ));
    }

    // Render Import/Export Page
    public function render_import_export_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Import/Export Projects', 'tanish-portfolio'); ?></h1>
            <div class="tanish-container">
                <!-- Export Section -->
                <div class="tanish-export">
                    <h2><?php _e('Export Projects', 'tanish-portfolio'); ?></h2>
                    <div class="column-to-be-export">
                        <label for="export_columns">Which columns should be exported?</label>
                        <select id="export_columns">
                            <option value="all"><?php _e('All Columns', 'tanish-portfolio'); ?></option>
                            <option value="title"><?php _e('Title', 'tanish-portfolio'); ?></option>
                            <option value="description"><?php _e('Description', 'tanish-portfolio'); ?></option>
                            <option value="category"><?php _e('Category', 'tanish-portfolio'); ?></option>
                            <option value="tags"><?php _e('Tags', 'tanish-portfolio'); ?></option>
                            <option value="start_date"><?php _e('Start Date', 'tanish-portfolio'); ?></option>
                            <option value="end_date"><?php _e('End Date', 'tanish-portfolio'); ?></option>
                        </select>
                    </div>

                    <div class="export-based-on-cat">
                        <label for="export_category">Which category should be exported?</label>
                        <select id="export_category">
                            <option value="all"><?php _e('All Categories', 'tanish-portfolio'); ?></option>
                            <!-- Categories will be populated dynamically -->
                        </select>
                    </div>

                    <div class="export-based-on-tag">
                        <label for="export_tags">Which tag type should be exported?</label>
                        <select id="export_tags">
                            <option value="all"><?php _e('All Tags', 'tanish-portfolio'); ?></option>
                            <!-- Tags will be populated dynamically -->
                        </select>
                    </div>

                    <button id="download_csv"><?php _e('Download CSV', 'tanish-portfolio'); ?></button>
                    <div id="export_progress"></div>
                </div>

                <!-- Import Section -->
                <div class="tanish-import">
                    <h2>Import Projects</h2>
                    <form id="import_form">
                        <div>
                            <label>Title:</label>
                            <input type="text" id="import_title" name="title" required>
                        </div>

                        <div>
                            <label>Description:</label>
                            <textarea id="import_description" name="description"></textarea>
                        </div>

                        <div>
                            <label>Categories (comma-separated):</label>
                            <input type="text" id="import_category" name="category" placeholder="E.g., Web Development, UI/UX">
                        </div>

                        <div>
                            <label>Tags (comma-separated):</label>
                            <input type="text" id="import_tags" name="tags" placeholder="E.g., React, WordPress, PHP">
                        </div>

                        <div>
                            <label>Start Date:</label>
                            <input type="date" id="import_start_date" name="start_date">
                        </div>

                        <div>
                            <label>End Date:</label>
                            <input type="date" id="import_end_date" name="end_date">
                        </div>

                        <div>
                            <label>Project Image:</label>
                            <input type="file" id="import_image" name="image">
                        </div>

                        <div class="upload">
                            <button type="submit" class="upload-btn">Upload</button>
                        </div>
                    </form>
                    <div id="import_status"></div>
                </div>

            </div>
        </div>
        <?php
    }
}


