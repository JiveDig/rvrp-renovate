<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Sets featured image fallback image ID.
 *
 * @return int
 */
function rvrp_get_featured_image_fallback() {
	return apply_filters( 'rvrp_default_featured_image_id', 0 );
}

/**
 * Sets avatar image fallback image ID.
 *
 * @return int
 */
function rvrp_get_avatar_fallback() {
	return apply_filters( 'rvrp_default_avatar_id', 0 );
}

/**
 * If current user is a renovator.
 *
 * @return bool
 */
function rvrp_is_renovator( $user_id = 0 ) {
	static $return = null;

	if ( ! is_null( $return ) ) {
		return $return;
	}

	if ( ! is_user_logged_in() ) {
		$return = false;
		return $return;
	}

	$user   = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
	$return = in_array( 'renovator', $user->roles );

	return $return;
}

/**
 * If user is viewing their own renovation.
 *
 * @param int $post_id The post ID to check. Uses current if none.
 *
 * @return bool
 */
function rvrp_is_renovators_renovation( $post_id = 0 ) {
	static $return = null;

	if ( ! is_null( $return ) ) {
		return $return;
	}

	if ( ! rvrp_is_renovator() ) {
		$return = false;
		return $return;
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$post_id = (int) $post_id;

	if ( 'renovation' !== get_post_type( $post_id ) ) {
		$is_profile = false;
		return $is_profile;
	}

	$is_profile = get_current_user_id() !== get_post_field( 'post_author', $post_id );

	return $is_profile;
}

/**
 * If user is viewing their own profile.
 *
 * @param int $post_id The post ID to check. Uses current if none.
 *
 * @return bool
 */
function rvrp_is_renovators_profile( $post_id = 0 ) {
	static $is_profile = null;

	if ( ! is_null( $is_profile ) ) {
		return $is_profile;
	}

	if ( ! rvrp_is_renovator() ) {
		$is_profile = false;
		return $is_profile;
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( 'mai_user' !== get_post_type( $post_id ) ) {
		$is_profile = false;
		return $is_profile;
	}

	if ( ! function_exists( 'maiup_get_user_post' ) ) {
		$is_profile = false;
		return $is_profile;
	}

	$user_id = get_current_user_id();
	$post    = maiup_get_user_post( $user_id );

	if ( ! $post ) {
		$is_profile = false;
		return $is_profile;
	}

	$is_profile = get_the_ID() === (int) $post->ID;

	return $is_profile;
}
