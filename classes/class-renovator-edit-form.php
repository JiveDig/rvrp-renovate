<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Renovator profile edit class.
 */
class RVRP_Renovator_Edit_Form {
	protected $post_id;
	protected $keys;

	/**
	 * Constructs the class.
	 *
	 * @return void
	 */
	function __construct( $post_id ) {
		$this->post_id = $post_id; // Sanitized in listener class.
		$this->keys    = $this->get_keys();
		$this->hooks();
	}

	/**
	 * Gets array of [ field name => keys ].
	 *
	 * @return array
	 */
	function get_keys() {
		$keys   = [];
		$groups = [
			acf_get_fields( 'rvrp_post_field_group' ),
			acf_get_fields( 'rvrp_post_sidebar_field_group' ),
			acf_get_fields( 'rvrp_post_info_field_group' ),
		];

		foreach ( $groups as $group ) {
			$keys = array_merge( $keys, wp_list_pluck( $group, 'key', 'name' ) );
		}

		// Remove image fields.
		unset( $keys['_thumbnail_id'] );
		unset( $keys['avatar_id'] );

		return $keys;
	}

	/**
	 * Runs hooks.
	 *
	 * @return void
	 */
	function hooks() {
		// Form listener.
		add_filter( 'acf/pre_save_post', [ $this, 'save_post' ] );
		add_action( 'genesis_loop',      [ $this, 'add_form' ] );
	}

	/**
	 * Saves post data to post.
	 *
	 * @param mixed $post_id The post ID.
	 *
	 * @return mixed
	 */
	function save_post( $post_id ) {
		$data = isset( $_POST['acf'] ) && ! empty( $_POST['acf'] ) ? $_POST['acf'] : [];

		if ( ! $data ) {
			return $post_id;
		}

		if ( get_the_ID() !== $post_id ) {
			return $post_id;
		}

		$update = false;
		$args   = [
			'ID' => $post_id,
		];

		// Field keys to update.
		$keys = [
			'post_title',
			'post_excerpt',
			'post_content',
		];

		foreach ( $keys as $key ) {
			if ( ! isset( $data[ $this->keys[ $key ] ] ) ) {
				continue;
			}

			// Add to post args.
			$args[ $key ] = wp_kses_post( $data[ $this->keys[ $key ] ] );

			// Unset key so it doesn't save to meta.
			unset( $data[ $this->keys[ $key ] ] );

			// We need to update the post.
			$update = true;
		}

		if ( ! $update ) {
			return $post_id;
		}

		wp_update_post( $args );

		return $post_id;
	}

	/**
	 * Display the form.
	 *
	 * @return void
	 */
	function add_form() {
		$post     = function_exists( 'maiup_get_user_post' ) ? maiup_get_user_post( get_current_user_id() ) : false;
		$redirect = $post ? get_permalink( $post->ID ) : '';

		printf( '<p class="has-lg-margin-bottom"><a href="%s">← %s</a></p>', esc_url( $redirect ), __( 'Back to my profile', 'rvrenopro' ) );
		printf( '<h1>%s %s</h1>', __( 'Edit', 'rvrenopro' ), get_the_title( $this->post_id ) );

		acf_form(
			[
				'fields'             => array_keys( $this->keys ),
				'post_id'            => $this->post_id,
				'submit_value'       => __( 'Save Changes', 'rvrenopro' ),
				'updated_message'    => __( 'Changes saved successfully.', 'rvrenopro' ),
				'html_submit_button' => '<input type="submit" class="button" value="%s" />',
				'return'             => esc_url( $redirect ),
			]
		);
	}
}
