<?php 

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get total profile visits
$total_visits = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tanish_profile_visits");

// Get top 3 projects by share count
$top_projects = $wpdb->get_results("
    SELECT project_id, share_count
    FROM {$wpdb->prefix}tanish_project_shares
    GROUP BY project_id
    ORDER BY share_count DESC
    LIMIT 3
");

// Get profile visits data for chart
$profile_visits_data = $wpdb->get_results("
    SELECT DATE(timestamp) as visit_date, COUNT(*) as count
    FROM {$wpdb->prefix}tanish_profile_visits
    GROUP BY visit_date 
    ORDER BY visit_date
");

// Check if profile visits exist
$has_profile_visits = !empty($profile_visits_data);

?>

<div class="wrap">
    <h1 style="text-align:center;">📊 Portfolio Analytics Dashboard</h1>
    
    <div class="analytics-cards">
        <div class="card">
            <h2>Total Profile Visits</h2>
            <select id="profile-visit-filter">
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month" selected>This Month</option>
            </select>
            <p id="profile-visit-count"><?php echo $total_visits; ?></p>
        </div>

        <div class="card">
            <h2>Most Shared Projects</h2>
            <ul>
                <?php foreach ($top_projects as $project): ?>
                    <li>
                        <strong><?php echo get_the_title($project->project_id); ?></strong> 
                        (<?php echo $project->share_count; ?> shares)
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Conditionally render Profile Visits Chart -->
    <?php if ($has_profile_visits): ?>
        <canvas id="profileVisitsChart"></canvas>
    <?php endif; ?>

    <canvas id="popularProjectsChart"></canvas>
</div>

<?php
// Enqueue Chart.js
wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
wp_enqueue_script('tanish-portfolio-analytics-js', plugin_dir_url(__FILE__) . '../js/tanish-portfolio-analytics.js', array('jquery', 'chart-js'), null, true);

// Pass AJAX URL & nonce
wp_localize_script('tanish-portfolio-analytics-js', 'tanishAjax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('tanish_portfolio_nonce')
));

// Pass data to JS
wp_localize_script('tanish-portfolio-analytics-js', 'analyticsData', array(
    'profileVisits' => $wpdb->get_results("SELECT DATE(timestamp) as visit_date, COUNT(*) as count
    FROM {$wpdb->prefix}tanish_profile_visits
    GROUP BY visit_date 
    ORDER BY visit_date"),
    'popularProjects' => $top_projects
));
