<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Tanish_Portfolio
 * @subpackage Tanish_Portfolio/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tanish_Portfolio
 * @subpackage Tanish_Portfolio/includes
 * @author     wisdmlabs <tanish.bharati@wisdmlabs.com>
 */

 if(!defined('ABSPATH')) {
	exit;
}

class Tanish_Portfolio_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// New table for project shares
        $table_project_shares = $wpdb->prefix . 'tanish_project_shares';
        $sql_project_shares = "CREATE TABLE IF NOT EXISTS $table_project_shares (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            project_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NULL,
            ip_address VARCHAR(45) NOT NULL,
            share_count INT UNSIGNED DEFAULT 0,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";

		// New table for profile visits
        $table_profile_visits = $wpdb->prefix . 'tanish_profile_visits';
        $sql_profile_visits = "CREATE TABLE IF NOT EXISTS $table_profile_visits (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            ip_address VARCHAR(45) NOT NULL,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql_project_shares);
        dbDelta($sql_profile_visits);

		// Drop the old tracking table if it exists**
        $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "tanish_portfolio_tracking");
	}

}
