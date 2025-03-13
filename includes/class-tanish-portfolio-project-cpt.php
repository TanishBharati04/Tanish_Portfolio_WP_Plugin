<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tanish_Portfolio_Project_CPT {
    
    public function __construct() {
        add_action('init', array($this, 'register_project_cpt'));
        add_filter('the_content', array($this, 'add_social_share_button'));

    }

    public function register_project_cpt() {
        $labels = array(
            'name'               => __('Projects', 'tanish-portfolio'),
            'singular_name'      => __('Project', 'tanish-portfolio'),
            'menu_name'          => __('Projects', 'tanish-portfolio'),
            'name_admin_bar'     => __('Project', 'tanish-portfolio'),
            'add_new'            => __('Add New', 'tanish-portfolio'),
            'add_new_item'       => __('Add New Project', 'tanish-portfolio'),
            'new_item'           => __('New Project', 'tanish-portfolio'),
            'edit_item'          => __('Edit Project', 'tanish-portfolio'),
            'view_item'          => __('View Project', 'tanish-portfolio'),
            'all_items'          => __('All Projects', 'tanish-portfolio'),
            'search_items'       => __('Search Projects', 'tanish-portfolio'),
            'not_found'          => __('No projects found', 'tanish-portfolio'),
            'not_found_in_trash' => __('No projects found in Trash', 'tanish-portfolio'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-portfolio',
            'query_var'          => true,
            'rewrite'            => array('slug' => 'projects'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions'),
            'taxonomies'         => array('category', 'post_tag'),
            'show_in_rest'       => true
        );

        register_post_type('project', $args);

        // 🔍 Debug: Log available taxonomies for 'project'
        // error_log("Registered Taxonomies for 'project': " . print_r(get_object_taxonomies('project', 'names'), true));
    }

    public function add_social_share_button($content) {
        if (is_singular('project') && in_the_loop() && is_main_query()) {
            ob_start();
            include plugin_dir_path(__FILE__) . '../public/partials/tanish-portfolio-social-share.php';
            $social_share_html = ob_get_clean();
            return $content . $social_share_html;
        }
        return $content;
    }
    
}
