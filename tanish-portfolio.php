<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wisdmlabs.com
 * @since             1.0.0
 * @package           Tanish_Portfolio
 *
 * @wordpress-plugin
 * Plugin Name:       Tanish Portfolio 
 * Plugin URI:        https://wisdmlabs.com
 * Description:       This is demo plugin for portfolio for training purpose
 * Version:           1.0.0
 * Author:            wisdmlabs
 * Author URI:        https://wisdmlabs.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tanish-portfolio
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Prevent direct access
if(!defined('ABSPATH')) {
	exit;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TANISH_PORTFOLIO_VERSION', '1.0.0' );

// Plugin name
define( 'TANISH_PORTFOLIO_PLUGIN_NAME', 'Tanish Portfolio' );

// Path
define( 'TANISH_PORTFOLIO_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tanish-portfolio-activator.php
 */
function activate_tanish_portfolio() {
	require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio-activator.php';
	Tanish_Portfolio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tanish-portfolio-deactivator.php
 */
function deactivate_tanish_portfolio() {
	require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio-deactivator.php';
	Tanish_Portfolio_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tanish_portfolio' );
register_deactivation_hook( __FILE__, 'deactivate_tanish_portfolio' );

function tanish_portfolio_init() {
    require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio.php';
    require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio-project-cpt.php';
    require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-project-meta-box.php';
	require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio-import-export.php';
	require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio-import-handler.php';
	require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio-export-handler.php';
	require_once TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio-share-handler.php';

    new Tanish_Portfolio_Project_CPT();
    new Tanish_Portfolio_Meta_Box();
	new Tanish_Portfolio_Import_Export();
	new Tanish_Portfolio_Import_Handler();
	new Tanish_Portfolio_Export_Handler();
	new Tanish_Portfolio_Share_Handler();

}
add_action('plugins_loaded', 'tanish_portfolio_init');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require TANISH_PORTFOLIO_PATH . 'includes/class-tanish-portfolio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tanish_portfolio() {

	$plugin = new Tanish_Portfolio();
	$plugin->run();

}
run_tanish_portfolio();
