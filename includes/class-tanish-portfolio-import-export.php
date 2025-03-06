<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Import_Export {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_import_export_submenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('upload_mimes', array($this, 'tanish_portfolio_allow_csv_uploads'));
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

        // Tabs Logic
        wp_enqueue_script('tanish-portfolio-tabs', plugins_url('../assets/tabs.js', __FILE__), array('jquery'), null, true);

        // CSV Import-Export Logic
        wp_enqueue_script('tanish-portfolio-import-export', plugins_url('../assets/import-export.js', __FILE__), array('jquery'), null, true);

        // Localize AJAX URL & nonce for security
        wp_localize_script('tanish-portfolio-import-export', 'tanishAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tanish_nonce')
        ));
    }

    function tanish_portfolio_allow_csv_uploads($mime_types) {
        $mime_types['csv'] = 'text/csv';
        return $mime_types;
    }

    // Render Import/Export Page
    public function render_import_export_page() {
        ?>
        <div class="wrap">
            <!-- Tab Navigation -->
            <div class="tab-container">
                <div class="tab active" id="import-tab">Import</div>
                <div class="tab" id="export-tab">Export</div>
            </div>


            <div class="content-container">
                <!-- Import Section -->
                <div id="import-section">
                    <h2><?php _e('Import Projects', 'tanish-portfolio'); ?></h2>
                    <label class="upload-box">
                        <input type="file" id="csv-file" accept=".csv">
                        Click to upload CSV file
                    </label>
                    <p>Give data in the same order as <a href="<?php echo plugin_dir_url(__FILE__) . 'assets/sample_projects.csv'; ?>" download>Sample File</a> in <strong>CSV Format</strong></p>
                    <button id="upload-btn">Upload</button>
                </div>

                <!-- Export Section (Hidden Initially) -->
                <div id="export-section" class="hidden">
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

                    <button id="export-project-btn"><?php _e('Export Project', 'tanish-portfolio'); ?></button>
                    <div id="export_progress"></div>
                </div>
            </div>

             <!-- Progress Modal -->
            <div class="progress-modal" id="progress-modal">
                <div class="modal-content">
                    <h3>Uploading...</h3>
                    <p id="progress-text">0 records uploaded</p>
                    <div class="progress-bar">
                        <div class="progress" id="progress-bar"></div>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }
}


