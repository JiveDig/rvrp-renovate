<?php

/**
 * Plugin Name:       RV Reno Pro - Renovate
 * Plugin URI:        https://rvrenopro.com/
 * GitHub Plugin URI: https://github.com/jivedig/rvrp-renovate/
 * Description:       Custom code for Renovators and Renovations.
 * Version:           1.0.6
 *
 * Author:            Mike Hemberger @JiveDig
 * Author URI:        https://bizbudding.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Plugin version.
if ( ! defined( 'RVRP_RENOVATE_VERSION' ) ) {
	define( 'RVRP_RENOVATE_VERSION', '1.0.6' );
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

// Include all php files in the /includes/ directory.
foreach ( glob( dirname( __FILE__ ) . '/includes/*.php' ) as $file ) { include $file; }
foreach ( glob( dirname( __FILE__ ) . '/classes/*.php' ) as $file ) { include $file; }

register_activation_hook( __FILE__, 'rvrp_plugin_activation' );
/**
 * Adds new role when plugin is activated.
 * Cloned from Subscriber.
 *
 * @return void
 */
function rvrp_plugin_activation() {
	$subscriber = get_role( 'subscriber' );
	add_role( 'renovator', __( 'Renovator', 'rvrenopro' ), $subscriber->capabilities );
}

/**
 * Allows renovators to upload media.
 *
 * @return void
 */
add_action( 'init', function() {
	if ( is_admin() ) {
		return;
	}

	$renovator = get_role( 'renovator' );
	$renovator->add_cap( 'upload_files' );
});

/**
 * Forces renovators to only see media they've uploaded.
 *
 * @return array
 */
add_filter( 'ajax_query_attachments_args', function( $query ) {
	$user_id = get_current_user_id();

	if ( ! rvrp_is_renovator( $user_id ) || current_user_can( 'manage_options' ) ) {
		return $query;
	}

	$query['author'] = $user_id;

	return $query;
});

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

add_filter( 'get_post_metadata', 'rvrp_featured_image_default', 10, 4 );
/**
 * Sets fallback featured image for renovators.
 * Must be named function so we can remove it prior to checking
 * for existing featured image.
 *
 * @param $value
 * @param $post_id
 * @param $meta_key
 * @param $single
 *
 * @return int
 */
function rvrp_featured_image_default( $value, $post_id, $meta_key, $single ) {
	// Bail if not a renovator.
	if ( 'mai_user' !== get_post_type( $post_id ) ) {
		return $value;
	}

	// Bail if not the key we want.
	if ( '_thumbnail_id' !== $meta_key ) {
		return $value;
	}

	// Bail if in admin.
	if ( is_admin() ) {
		return $value;
	}

	// Remove filter to avoid loopbacks.
	remove_filter( 'get_post_metadata', 'rvrp_featured_image_default', 10, 4 );

	// Check for an existing featured image.
	$image_id = get_post_thumbnail_id( $post_id );

	// Add back our filter.
	add_filter( 'get_post_metadata', 'rvrp_featured_image_default', 10, 4 );

	// Bail if we already have a featured image.
	if ( $image_id ) {
		return $image_id;
	}

	return rvrp_get_featured_image_fallback();
}

add_action( 'mai_after_entry_image', 'rvrp_do_avatar', 10, 2 );
/**
 * Adds avatar image after entry image.
 *
 * @param WP_Post $entry The post object.
 * @param array   $args  The markup args.
 *
 * @return void
 */
function rvrp_do_avatar( $entry, $args ) {
	if ( ! isset( $entry->post_type ) || ! in_array( $entry->post_type, [ 'mai_user', 'renovation' ] ) ) {
		return;
	}

	static $first  = true;
	$avatar = $css = '';
	$image_id      = rvrp_get_avatar_id( $entry->ID );
	$profile_url   = get_permalink( $entry->ID );

	if ( $first && $image_id ) {
		ob_start();
		?>
		<style>
		.rvrp-avatar {
			position: relative;
			display: block;
			max-width: min(200px, 33.333333%);
			margin: var(--rvrp-avatar-margin-top, 0) auto var(--rvrp-avatar-margin-bottom, var(--spacing-lg));
			overflow: hidden;
			text-align: center;
			border: 6px solid white;
			border-radius: 9999px;
		}
		.entry-image-link {
			margin-bottom: 0;
		}
		.entry-image-link + .rvrp-avatar {
			--rvrp-avatar-margin-top: calc(min(200px, 33.333333%) * -0.5);
		}
		.mai-grid {
			--rvrp-avatar-margin-bottom: 0;
		}
		.rvrp-avatar__image {
			display: block;
			width: 100%;
		}
		</style>
		<?php
		$css   = ob_get_clean();
		$first = false;
	}


	if ( $image_id ) {
		$single  = is_singular( 'mai_user' );
		$avatar .= '<div class="rvrp-avatar">';
			$avatar .= $css;
			$avatar .= ! $single ? sprintf( '<a href="%s">', $profile_url ) : '';
				$image   = (string) wp_get_attachment_image( $image_id, 'square-sm', false, [ 'class' => 'rvrp-avatar__image' ] );
				if ( $single ) {
					$image = apply_filters( 'rvrp_avatar', $image );
				}
				$avatar .= $image;
			$avatar .= ! $single ? '</a>' : '';
		$avatar .= '</div>';
	}

	echo $avatar;
}
