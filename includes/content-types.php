<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Register custom content types.
 *
 * @return void
 */
add_action( 'init', function() {
	// Renovation.
	register_post_type( 'renovation', [
		'exclude_from_search' => false,
		'has_archive'         => true,
		'hierarchical'        => false,
		'labels'              => [
			'name'               => _x( 'RV Renovations', 'RV Renovation general name', 'rvrenopro' ),
			'singular_name'      => _x( 'RV Renovation', 'RV Renovation singular name', 'rvrenopro' ),
			'menu_name'          => _x( 'RV Renovations', 'RV Renovation admin menu', 'rvrenopro' ),
			'name_admin_bar'     => _x( 'RV Renovation', 'RV Renovation add new on admin bar', 'rvrenopro' ),
			'add_new'            => _x( 'Add New', 'RV Renovation', 'rvrenopro' ),
			'add_new_item'       => __( 'Add New RV Renovation',  'rvrenopro' ),
			'new_item'           => __( 'New RV Renovation', 'rvrenopro' ),
			'edit_item'          => __( 'Edit RV Renovation', 'rvrenopro' ),
			'view_item'          => __( 'View RV Renovation', 'rvrenopro' ),
			'all_items'          => __( 'All RV Renovations', 'rvrenopro' ),
			'search_items'       => __( 'Search RV Renovations', 'rvrenopro' ),
			'parent_item_colon'  => __( 'Parent RV Renovations:', 'rvrenopro' ),
			'not_found'          => __( 'No RV Renovations found.', 'rvrenopro' ),
			'not_found_in_trash' => __( 'No RV Renovations found in Trash.', 'rvrenopro' )
		],
		'menu_icon'          => 'dashicons-art',
		'public'             => true,
		'publicly_queryable' => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => true,
		'show_in_rest'       => false, // Use Classic Editor since it's mostly ACF based.
		'show_ui'            => true,
		'rewrite'            => [ 'slug' => 'renovations', 'with_front' => false ],
		'supports'           => [ 'title', 'editor', 'author', 'page-attributes', 'genesis-cpt-archives-settings', 'mai-archive-settings', 'mai-single-settings' ],
		'taxonomies'         => [ 'renovation_type' ],
	] );

	// Renovation/RV Types.
	register_taxonomy( 'renovation_type', [ 'renovation' ], [
		'hierarchical'               => true,
		'labels'                     => [
			'name'                       => _x( 'RV Types', 'RV Type General Name', 'textdomain' ),
			'singular_name'              => _x( 'RV Type', 'RV Type Singular Name', 'textdomain' ),
			'menu_name'                  => __( 'RV Types', 'textdomain' ),
			'all_items'                  => __( 'All Items', 'textdomain' ),
			'parent_item'                => __( 'Parent Item', 'textdomain' ),
			'parent_item_colon'          => __( 'Parent Item:', 'textdomain' ),
			'new_item_name'              => __( 'New Item Name', 'textdomain' ),
			'add_new_item'               => __( 'Add New Item', 'textdomain' ),
			'edit_item'                  => __( 'Edit Item', 'textdomain' ),
			'update_item'                => __( 'Update Item', 'textdomain' ),
			'view_item'                  => __( 'View Item', 'textdomain' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'textdomain' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'textdomain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'textdomain' ),
			'popular_items'              => __( 'Popular Items', 'textdomain' ),
			'search_items'               => __( 'Search Items', 'textdomain' ),
			'not_found'                  => __( 'Not Found', 'textdomain' ),
		],
		'meta_box_cb'                => false, // Set false to hide metabox.
		'public'                     => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_in_rest'               => true,
		'show_in_quick_edit'         => true,
		'show_tagcloud'              => true,
		'show_ui'                    => true,
		'rewrite'                    => [ 'slug' => 'types', 'with_front' => false ],
	] );

	// Renovator Types.
	register_taxonomy( 'renovator_type', [ 'mai_user' ], [
		'hierarchical'               => true,
		'labels'                     => [
			'name'                       => _x( 'Types', 'Type General Name', 'textdomain' ),
			'singular_name'              => _x( 'Type', 'Type Singular Name', 'textdomain' ),
			'menu_name'                  => __( 'Types', 'textdomain' ),
			'all_items'                  => __( 'All Items', 'textdomain' ),
			'parent_item'                => __( 'Parent Item', 'textdomain' ),
			'parent_item_colon'          => __( 'Parent Item:', 'textdomain' ),
			'new_item_name'              => __( 'New Item Name', 'textdomain' ),
			'add_new_item'               => __( 'Add New Item', 'textdomain' ),
			'edit_item'                  => __( 'Edit Item', 'textdomain' ),
			'update_item'                => __( 'Update Item', 'textdomain' ),
			'view_item'                  => __( 'View Item', 'textdomain' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'textdomain' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'textdomain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'textdomain' ),
			'popular_items'              => __( 'Popular Items', 'textdomain' ),
			'search_items'               => __( 'Search Items', 'textdomain' ),
			'not_found'                  => __( 'Not Found', 'textdomain' ),
		],
		'meta_box_cb'                => null, // Set false to hide metabox.
		'public'                     => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_in_rest'               => true,
		'show_in_quick_edit'         => true,
		'show_tagcloud'              => true,
		'show_ui'                    => true,
		'rewrite'                    => false,
	] );

}, 10 );

/**
 * Removes Genesis Layouts support.
 *
 * Use classic editor for Renovator post.
 * Since we're allowing front end editing we only need plain HTML for content.
 *
 * @param array $args The post type args.
 *
 * @return array
 */
add_filter( 'maiup_post_type_args', function( $args ) {
	$args['supports']     = array_diff( $args['supports'], [ 'genesis-layouts' ] );
	$args['show_in_rest'] = false;

	return $args;
});

/**
 * Change Mai User Post type plural label.
 *
 * @param string $label
 *
 * @return string
 */
add_filter( 'maiup_post_type_plural', function( $label ) {
	return __( 'RV Renovators', 'textdomain' );
});

/**
 * Change Mai User Post type singular label.
 *
 * @param string $label
 *
 * @return string
 */
add_filter( 'maiup_post_type_singular', function( $label ) {
	return __( 'RV Renovator', 'textdomain' );
});

/**
 * Change Mai User Post type base url.
 *
 * @param string $base
 *
 * @return string
 */
add_filter( 'maiup_post_type_base', function( $base ) {
	return 'renovators';
});

/**
 * Enable user roles.
 *
 * @param array $roles
 *
 * @return array
 */
add_filter( 'maiup_user_roles', function( $roles ) {
	return [ 'renovator' ];
});

/**
 * Enables meta keys to sync from user to post meta.
 *
 * @param array $meta_keys The meta key names.
 *
 * @return array
 */
add_filter( 'maiup_meta_keys', function( $meta_keys ) {
	$meta_keys = array_merge( $meta_keys,
		[
			'_thumbnail_id',
			'website',
			'instagram',
			'twitter',
			'facebook',
			'youtube',
			'pinterest',
		]
	);

	return $meta_keys;
});

/**
 * Enables ACF meta keys to sync from user to post meta.
 *
 * @uses Advanced Custom Fields.
 *
 * @param array $meta_keys The meta key names.
 *
 * @return array
 */
add_filter( 'maiup_acf_keys', function( $meta_keys ) {
	$meta_keys[] = 'avatar_id';

	return $meta_keys;
});

/**
 * Change author URL to renovator profile.
 *
 * @param string $link            The URL to the author's page.
 * @param int    $author_id       The author's ID.
 * @param string $author_nicename The author's nice name.
 *
 * @return url
 */
add_filter( 'author_link', function( $link, $author_id, $author_nicename ) {
	if ( rvrp_is_renovator( $author_id ) && ! user_can( $author_id, 'edit_posts' ) ) {
		$post = maiup_get_user_post( $author_id );

		if ( $post ) {
			return get_permalink( $post->ID );
		}
	}

	return $link;

}, 10, 3 );

/**
 * Redirect author archive to renovator profile.
 *
 * @return void
 */
add_action( 'template_redirect', function() {
	if ( ! is_author() ) {
		return;
	}

	$user_id = get_queried_object_id();

	if ( ! $user_id ) {
		return;
	}

	if ( ! rvrp_is_renovator( $user_id ) || user_can( $user_id, 'edit_posts' ) ) {
		return;
	}

	$post = maiup_get_user_post( $user_id );

	if ( ! $post ) {
		return;
	}

	wp_redirect( get_permalink( $post->ID ), 301 );
	exit();
});

/**
 * Use meta field as avatar.
 *
 * @return string
 */
add_filter( 'get_avatar', function( $avatar, $id_or_email, $size, $default, $alt ) {
	$user = '';

	// Get user by id or email.
	if ( is_numeric( $id_or_email ) ) {
		$id   = (int) $id_or_email;
		$user = get_user_by( 'id' , $id );
	} elseif ( is_object( $id_or_email ) ) {
		if ( ! empty( $id_or_email->user_id ) ) {
			$id   = (int) $id_or_email->user_id;
			$user = get_user_by( 'id' , $id );
		}
	} else {
		$user = get_user_by( 'email', $id_or_email );
	}

	// Bail if no user.
	if ( ! $user ) {
		return $avatar;
	}

	// Get the user id.
	$user_id = $user->ID;

	// Get the file id.
	$image_id = absint( get_user_meta( $user_id, 'avatar_id', true ) );

	// Bail if we don't have a local avatar.
	if ( ! $image_id ) {
		return $avatar;
	}

	// Get image HTML.
	$image_size = is_admin() ? 'thumbnail' : 'square-sm';
	$image_html = wp_get_attachment_image( $image_id, $image_size, false,
		[
			'class'  => sprintf( 'avatar avatar-%s', $size ),
			'style'  => sprintf( 'max-width:%spx;height:auto;border-radius:50%%;', $size ),
		]
	);

	// Return our new avatar if we have it.
	return $image_html ?: $avatar;

}, 10, 5 );
