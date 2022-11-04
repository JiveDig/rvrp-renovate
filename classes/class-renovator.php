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
