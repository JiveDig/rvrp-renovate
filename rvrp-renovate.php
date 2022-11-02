<?php

/**
 * Plugin Name:       RV Reno Pro - Renovate
 * Plugin URI:        https://rvrenopro.com/
 * GitHub Plugin URI: https://github.com/jivedig/rvrp-renovate/
 * Description:       Custom code for Renovators and Renovations.
 * Version:           1.0.2
 *
 * Author:            Mike Hemberger @JiveDig
 * Author URI:        https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin version.
if ( ! defined( 'RVRP_RENOVATE_VERSION' ) ) {
	define( 'RVRP_RENOVATE_VERSION', '1.0.2' );
}

// Plugin Folder Path.
if ( ! defined( 'RVRP_RENOVATE_PLUGIN_DIR' ) ) {
	define( 'RVRP_RENOVATE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL.
if ( ! defined( 'RVRP_RENOVATE_PLUGIN_URL' ) ) {
	define( 'RVRP_RENOVATE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Plugin Root File.
if ( ! defined( 'RVRP_RENOVATE_PLUGIN_FILE' ) ) {
	define( 'RVRP_RENOVATE_PLUGIN_FILE', __FILE__ );
}

// Plugin Base Name
if ( ! defined( 'RVRP_RENOVATE_BASENAME' ) ) {
	define( 'RVRP_RENOVATE_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
}

register_activation_hook( __FILE__, 'rvrp_plugin_activation' );
/**
 * Adds new role when plugin is activated.
 * Cloned from Subscriber.
 *
 * @return void
 */
function rvrp_plugin_activation() {
	$wp_roles = wp_roles();

	if ( ! $wp_roles ) {
		return;
	}

	$subscriber = $wp_roles->get_role( 'subscriber' );

	$wp_roles->add_role( 'renovator', __( 'Renovator', 'rvrenopro' ), $subscriber->capabilities );
}

/**
 * Maybe add updater.
 *
 * @return void
 */
add_action( 'plugins_loaded', function() {
	// Bail if current user cannot manage plugins.
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	// Bail if plugin updater is not loaded.
	if ( ! class_exists( 'Puc_v4_Factory' ) ) {
		return;
	}

	// Setup the updater.
	$updater = Puc_v4_Factory::buildUpdateChecker( 'https://github.com/JiveDig/rvrp-renovate', __FILE__, 'rvrenopro' );

	// Set the stable branch.
	$updater->setBranch( 'main' );

	// Maybe set github api token.
	if ( defined( 'MAI_GITHUB_API_TOKEN' ) ) {
		$updater->setAuthentication( MAI_GITHUB_API_TOKEN );
	}

	// Add icons for Dashboard > Updates screen.
	if ( function_exists( 'mai_get_updater_icons' ) && $icons = mai_get_updater_icons() ) {
		$updater->addResultFilter(
			function ( $info ) use ( $icons ) {
				$info->icons = $icons;
				return $info;
			}
		);
	}
});

// Include all php files in the /includes/ directory.
foreach ( glob( dirname( __FILE__ ) . '/includes/*.php' ) as $file ) { include $file; }
foreach ( glob( dirname( __FILE__ ) . '/classes/*.php' ) as $file ) { include $file; }
