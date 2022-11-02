<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Renovator profile class.
 */
class RVRP_Renovator {
	/**
	 * Constructs the class.
	 *
	 * @return void
	 */
	function __construct() {
		$this->run();
	}

	/**
	 * Gets the ACF form head and runs all hooks.
	 *
	 * @return void
	 */
	function run() {
		// Enqueue CSS.
		add_action( 'wp_enqueue_scripts',    [ $this, 'enqueue' ] );
		// Add featured image fallback.
		add_filter( 'get_post_metadata',     [ $this, 'do_featured_image' ], 10, 4 );
		// Add avatar.
		add_action( 'mai_after_entry_image', [ $this, 'do_avatar' ], 10, 2 );
		// Add website and social links.
		add_action( 'mai_after_entry_title', [ $this, 'do_links' ], 10, 2 );
	}

	/**
	 * Enqueues CSS files.
	 *
	 * @return void
	 */
	function enqueue() {
		$file      = 'assets/css/renovator.css';
		$version   = RVRP_RENOVATE_VERSION;
		$file_path = RVRP_RENOVATE_PLUGIN_DIR . $file;
		$file_url  = RVRP_RENOVATE_PLUGIN_URL . $file;

		if ( file_exists( $file_path ) ) {
			$version .= '.' . date( 'njYHi', filemtime( $file_path ) );
			wp_enqueue_style( 'rvrp-renovator', $file_url, [], $version );
		}
	}

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
	function do_featured_image( $value, $post_id, $meta_key, $single ) {
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
		remove_filter( 'get_post_metadata', [ $this, 'do_featured_image' ], 10, 4 );

		// Check for an existing featured image.
		$image_id = get_post_thumbnail_id( $post_id );

		// Add back our filter.
		add_filter( 'get_post_metadata', [ $this, 'do_featured_image' ], 10, 4 );

		// Bail if we already have a featured image.
		if ( $image_id ) {
			return $image_id;
		}

		return rvrp_get_featured_image_fallback();
	}

	/**
	 * Adds avatar image after entry image.
	 *
	 * @param WP_Post $entry The post object.
	 * @param array   $args  The markup args.
	 *
	 * @return void
	 */
	function do_avatar( $entry, $args ) {
		if ( 'mai_user' !== $entry->post_type ) {
			return;
		}

		$avatar   = '';
		$image_id = $this->get_avatar_id();

		if ( $image_id ) {
			$avatar .= '<div class="rvrp-avatar">';
				$image   = wp_get_attachment_image( $image_id, 'square-sm', false, [ 'class' => 'rvrp-avatar__image' ] );
				$avatar .= apply_filters( 'rvrp_avatar', $image );
			$avatar .= '</div>';
		}

		echo $avatar;
	}

	/**
	 * Gets the avatar ID.
	 *
	 * @return int
	 */
	function get_avatar_id() {
		static $image_id = null;

		if ( ! is_null( $image_id ) ) {
			return $image_id;
		}

		$image_id = get_post_meta( get_the_ID(), 'avatar_id', true );
		$image_id = $image_id ?: rvrp_get_avatar_fallback();

		return $image_id;
	}

	/**
	 * Displays website and social links.
	 *
	 * @param WP_Post $entry The post object.
	 * @param array   $args  The markup args.
	 *
	 * @return void
	 */
	function do_links( $entry, $args ) {
		if ( ! isset( $args['context'] ) || 'single' !== $args['context'] ) {
			return;
		}

		$lis     = [];
		$post_id = get_the_ID();
		$icons   = [];
		$links   = [
			'instagram' => 'instagram',
			'twitter'   => 'twitter',
			'facebook'  => 'facebook-f',
			'youtube'   => 'youtube',
			'pinterest' => 'pinterest',
		];

		$website = get_post_meta( $post_id, 'website', true );

		if ( $website ) {
			$parts = wp_parse_url( esc_url( $website ) );
			$lis[] = sprintf( '<li class="rvrp-link--website"><a href="%s" title="%s">%s</a></li>', esc_url( $website ), __( 'Website', 'rvrenopro' ), $parts['host'] );
		}

		foreach ( $links as $name => $icon ) {
			$value = get_post_meta( $post_id, $name, true );

			if ( ! $value ) {
				continue;
			}

			// If only have a handle.
			if ( in_array( $name, [ 'instagram', 'twitter' ] ) ) {
				// If $value does not contain 'http'.
				if ( false === strpos( $value, 'http' ) ) {
					$value = sprintf( 'https://%s.com/%s', $name, untrailingslashit( sanitize_key( ltrim( $value, '@' ) ) ) );
				}
			}

			$icon = mai_get_icon(
				[
					'icon'       => $icon,
					'style'      => 'brands',
					'size'       => '1.25em',
					'padding'    => '8px',
					'color_icon' => 'heading',
				]
			);

			$lis[] = sprintf( '<li><a href="%s" title="%s">%s</a></li>', esc_url( $value ), ucwords( $name ), $icon ?: ucwords( $name ) );
		}

		$lis = apply_filters( 'rvrp_lis', $lis );

		if ( ! $lis ) {
			return;
		}

		printf( '<div class="rvrp-links"><ul class="rvrp-links__list">%s</ul></div>', implode( '', $lis ) );
	}
}

/**
 * Instantiates the class.
 *
 * @return void
 */
add_action( 'template_redirect', function() {
	if ( ! is_singular( 'mai_user' ) ) {
		return;
	}

	new RVRP_Renovator;
});
