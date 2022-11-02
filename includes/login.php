<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Registers logout url shortcode.
 *
 * @param array $atts The shortcode atts.
 *
 * @return string
 */
add_shortcode( 'rvrp_logout_url', function( $atts ) {
	// Atts.
	$atts = shortcode_atts(
		[
			'redirect' => '',
		],
		$atts,
		'rvrp_logout_url'
	);

	// Sanitize.
	$atts['redirect'] = $atts['redirect'] ? esc_url( $atts['redirect'] ) : '';

	return wp_logout_url( $atts['redirect'] );
});

/**
 * Changes login redirect to renovators profile.
 *
 * @param string           $url          The redirect destination URL.
 * @param string           $redirect_url The requested redirect destination URL passed as a parameter.
 * @param WP_User|WP_Error $user         WP_User object if login was successful, WP_Error object otherwise.
 *
 * @return string
 */
add_filter( 'login_redirect', function( $url, $redirect_url, $user ) {
	if ( ! $user || is_wp_error( $user ) ) {
		return $url;
	}

	if ( isset( $user->roles ) && in_array( 'renovator', (array) $user->roles ) ) {
		$post = maiup_get_user_post( $user->ID );
		return $post ? get_permalink( $post ) : get_post_type_archive_link( 'renovator' );
	}

	return $url;

}, 10, 3 );

