<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Add new Mai Content Area.
 *
 * @param array $parts The existing template parts.
 *
 * @return array
 */
add_filter( 'mai_template-parts_config', function( $parts ) {
	$parts['single-renovator'] = [
		'hook'       => 'genesis_entry_content',
		'priority'   => 12,
		'menu_order' => 80,
		'condition'  => function() {
			return is_singular( 'mai_user' );
		},
	];

	return $parts;
});

/**
 * Sets Mai Post Grid query author to Renovator user ID.
 *
 * @param array $query_args WP_Query args.
 * @param array $args       Mai Post Grid args.
 *
 * @return array
 */
add_filter( 'mai_post_grid_query_args', function( $query_args, $args ) {
	if ( ! isset( $args['class'] ) || empty( $args['class'] ) ) {
		return $query_args;
	}

	if ( ! mai_has_string( 'current-post-author', $args['class'] ) ) {
		return $query_args;
	}

	$query_args['author__in'] = (array) get_post_meta( get_the_ID(), 'mai_user_id', true );

	if ( rvrp_is_renovators_profile() ) {
		$query_args['post_status']   = (array) $query_args['post_status'];
		$query_args['post_status'][] = 'pending';
	}

	return $query_args;

}, 10, 3 );

/**
 * Adds button to renovator post on renovator author box.
 *
 * @param  string $description The existing description.
 * @param  string $context     Allows different author box markup for different contexts, specifically 'single'.
 *
 * @return string
 */
add_filter( 'genesis_author_box_description', function( $description, $context, $user_id ) {
	if ( rvrp_is_renovator( $user_id ) ) {
		$profile = function_exists( 'maiup_get_user_post' ) ? maiup_get_user_post( $user_id ) : 0;

		if ( $profile ) {
			$description .= sprintf( '<p><a class="button button-link has-no-padding-left" href="%s">%s</a></p>', get_permalink( $profile->ID ),__( 'View Profile', 'rvrenopro' ) );
		}
	}

	return $description;

}, 10, 3 );
