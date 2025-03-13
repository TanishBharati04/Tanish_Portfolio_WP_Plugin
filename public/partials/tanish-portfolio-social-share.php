<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get the current project URL
$project_url = get_permalink();
$project_title = get_the_title();
$share_count = get_post_meta(get_the_ID(), 'instagram_share_count', true);
$share_count = $share_count ? $share_count : 0;

?>

<div class="social-share-container">
    <button class="instagram-share-btn" data-project-id="<?php echo get_the_ID(); ?>" data-project-url="<?php echo esc_url($project_url); ?>" data-project-title="<?php echo esc_attr($project_title); ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
            <polyline points="16 6 12 2 8 6" />
            <line x1="12" y1="2" x2="12" y2="15" />
        </svg>
    </button>
    <p class="share-count-display">Shared: <span id="share-count-<?php echo get_the_ID(); ?>"><?php echo $share_count; ?></span> times</p>
</div>
