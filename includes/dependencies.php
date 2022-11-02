<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Dependency plugins.
 *
 * @param array $config The existing global styles config.
 *
 * @return array
 */
add_filter( 'mai_plugins_config', function( $plugins ) {
	$plugins['mai-user-post/mai-user-post.php'] = [
		'name'     => 'Mai User Post',
		'host'     => 'github',
		'uri'      => 'maithemewp/mai-user-post',
		'url'      => 'https://bizbudding.com/mai-theme/',
		'branch'   => 'master',
		'demos'    => [],
		'optional' => false,
	];

	$plugins['mai-popups/mai-popups.php'] = [
		'name'     => 'Mai Popups',
		'host'     => 'github',
		'uri'      => 'maithemewp/mai-popups',
		'url'      => 'https://bizbudding.com/mai-theme/',
		'branch'   => 'master',
		'demos'    => [],
		'optional' => false,
	];

	$plugins['mai-galleries/mai-galleries.php'] = [
		'name'     => 'Mai Galleries',
		'host'     => 'github',
		'uri'      => 'maithemewp/mai-galleries',
		'url'      => 'https://bizbudding.com/mai-theme/',
		'branch'   => 'master',
		'demos'    => [],
		'optional' => false,
	];

	return $plugins;
});

add_filter( 'plugin_action_links_mai-user-post/mai-user-post.php', 'rvrp_change_plugin_dependency_text', 100 );
add_filter( 'plugin_action_links_mai-popups/mai-popups.php',       'rvrp_change_plugin_dependency_text', 100 );
add_filter( 'plugin_action_links_rvrp-renovate/rvrp-renovate.php', 'rvrp_change_plugin_dependency_text', 100 );
/**
 * Changes plugin dependency text.
 *
 * @since 0.1.0
 *
 * @param array $actions Plugin action links.
 *
 * @return array
 */
function rvrp_change_plugin_dependency_text( $actions ) {
	$actions['required-plugin'] = sprintf(
		'<span class="network_active">%s</span>',
		__( 'RV Reno Pro Dependency', 'mai-engine' )
	);

	return $actions;
}
