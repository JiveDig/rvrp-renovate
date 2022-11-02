<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Renovator profile edit class.
 */
class RVRP_Renovator_Edit {
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
		add_action( 'wp_enqueue_scripts',                      [ $this, 'enqueue' ] );
		// Header code.
		add_action( 'get_header',                              [ $this, 'get_form_head' ] );
		// Field filters.
		add_filter( 'gettext',                                 [ $this, 'translate_text' ], 10, 3 );
		add_filter( 'acf/load_value/name=_thumbnail_id',       [ $this, 'load_thumbnail' ], 10, 3 );
		add_filter( 'acf/load_value/name=post_title',          [ $this, 'load_title' ], 10, 3 );
		add_filter( 'acf/load_value/name=post_excerpt',        [ $this, 'load_excerpt' ], 10, 3 );
		add_filter( 'acf/load_value/name=post_content',        [ $this, 'load_content' ], 10, 3 );
		// Edit forms.
		add_filter( 'genesis_markup_entry-image-link_content', [ $this, 'do_featured_image_edit' ], 10, 2 );
		add_filter( 'genesis_markup_entry-image-link_content', [ $this, 'do_profile_edit' ], 10, 2 );
		add_filter( 'rvrp_avatar',                             [ $this, 'do_avatar_edit' ] );
		add_filter( 'acf/validate_field',                      [ $this, 'handle_wysiwyg' ] );
		// Renovation submission.
		add_filter( 'genesis_entry_content',                   [ $this, 'do_renovation_actions' ] );
		// Check for pending renovations.
		add_filter( 'the_title',                               [ $this, 'do_pending_renovations' ], 10, 2 );
	}

	/**
	 * Enqueues CSS files.
	 *
	 * @return void
	 */
	function enqueue() {
		$file      = 'assets/css/renovate-edit.css';
		$version   = RVRP_RENOVATE_VERSION;
		$file_path = RVRP_RENOVATE_PLUGIN_DIR . $file;
		$file_url  = RVRP_RENOVATE_PLUGIN_URL . $file;

		if ( file_exists( $file_path ) ) {
			$version .= '.' . date( 'njYHi', filemtime( $file_path ) );
			wp_enqueue_style( 'rvrp-renovator-edit', $file_url, [], $version );
		}
	}

	/**
	 * Gets header code.
	 *
	 * @return void
	 */
	function get_form_head() {
		acf_form_head();
	}

	/**
	 * Translates text strings.
	 * Removes "No image selected" text from Gallery/Image fields.
	 *
	 * @param   string $translated_text
	 * @param   string $text
	 * @param   string $domain
	 *
	 * @return  string
	 */
	function translate_text( $translated_text, $text, $domain ) {
		if ( 'acf' !== $domain ) {
			return $translated_text;
		}

		switch ( $translated_text ) {
			case 'No image selected':
				$translated_text = '';
			break;
		}

		return $translated_text;
	}

	/**
	 * Sets featured image field to actual featured image.
	 *
	 * @param mixed      $value   The field value.
	 * @param int|string $post_id The post ID where the value is saved.
	 * @param array      $field   The field array containing all settings.
	 *
	 * @return string
	 */
	function load_thumbnail( $value, $post_id, $field ) {
		$image_id = (int) get_post_meta( $post_id, '_thumbnail_id', true );
		return rvrp_get_featured_image_fallback() !== $image_id ? $image_id : 0;
	}

	/**
	 * Sets post title field to actual post title.
	 *
	 * @param mixed      $value   The field value.
	 * @param int|string $post_id The post ID where the value is saved.
	 * @param array      $field   The field array containing all settings.
	 *
	 * @return string
	 */
	function load_title( $value, $post_id, $field ) {
		return (string) get_post_field( 'post_title', get_post( $post_id ) );
	}

	/**
	 * Sets post excerpt field to actual post excerpt.
	 *
	 * @param mixed      $value   The field value.
	 * @param int|string $post_id The post ID where the value is saved.
	 * @param array      $field   The field array containing all settings.
	 *
	 * @return string
	 */
	function load_excerpt( $value, $post_id, $field ) {
		return (string) get_post_field( 'post_excerpt', get_post( $post_id ) );
	}

	/**
	 * Sets post content field to actual post content.
	 *
	 * @param mixed      $value   The field value.
	 * @param int|string $post_id The post ID where the value is saved.
	 * @param array      $field   The field array containing all settings.
	 *
	 * @return string
	 */
	function load_content( $value, $post_id, $field ) {
		return (string) get_post_field( 'post_content', get_post( $post_id ) );
	}

	/**
	 * Gets the featured image edit link and form.
	 *
	 * @param string $content The content.
	 * @param array  $args    The markup args.
	 *
	 * @return string
	 */
	function do_featured_image_edit( $content, $args ) {
		if ( ! isset( $args['params']['args']['context'] ) || 'single' !== $args['params']['args']['context'] ) {
			return $content;
		}

		$content .= sprintf( '<a class="entry-image__edit rvrp-edit-link" href="#mai-popup-banner">%s%s</a>', $this->get_camera_icon(), __( 'Edit', 'rvrenopro' ) );
		$this->do_popup( 'mai-popup-banner', $this->get_form( [ '_thumbnail_id' ] ) );
		return $content;
	}

	/**
	 * Gets the profile edit link.
	 *
	 * @param string $content The content markup.
	 * @param array  $args    The markup args.
	 *
	 * @return string
	 */
	function do_profile_edit( $content, $args ) {
		if ( ! isset( $args['params']['args']['context'] ) || 'single' !== $args['params']['args']['context'] ) {
			return $content;
		}

		$url = add_query_arg( [
			'rvrp_renovator' => $args['params']['entry']->ID,
		], get_permalink() );

		$content .= sprintf( '<a class="rvrp-profile__edit rvrp-edit-link" href="%s">%s</a>', esc_url( $url ), __( 'Edit Profile', 'rvrenopro' ) );
		return $content;
	}

	/**
	 * Gets the avatar edit link and form.
	 *
	 * @param string $avatar The image HTML.
	 *
	 * @return string
	 */
	function do_avatar_edit( $image ) {
		$image .= sprintf( '<a class="rvrp-avatar__edit rvrp-edit-link" href="#mai-popup-avatar">%s%s</a>', $this->get_camera_icon(), __( 'Edit', 'rvrenopro' ) );
		$this->do_popup( 'mai-popup-avatar', $this->get_form( [ 'avatar_id' ] ) );

		return $image;
	}

	/**
	 * Sets post_content field in acf_form to simplified wysiwyg.
	 *
	 * @param array $field The field data.
	 *
	 * @return array
	 */
	function handle_wysiwyg( $field ) {
		if ( 'wysiwyg' !== $field['type'] || '_post_content' !== $field['key'] ) {
			return $field;
		}

		$field['label']        = '';
		$field['tabs']         = 'visual';
		$field['toolbar']      = 'basic';
		$field['media_upload'] = 0;

		return $field;
	}

	/**
	 * Display a popup.
	 *
	 * @param string $id      The HTML id.
	 * @param string $content The popup content.
	 *
	 * @return void
	 */
	function do_popup( $id, $content ) {
		if ( ! function_exists( 'mai_do_popup' ) ) {
			return;
		}

		mai_do_popup(
			[
				'id'       => $id,
				'trigger'  => 'manual',
				'position' => 'center center',
				'class'    => 'rvrp-popup',
			],
			$content
		);
	}

	/**
	 * Gets an edit form.
	 *
	 * @param string|array $fields The fields to display in the edit form.
	 *
	 * @return string
	 */
	function get_form( $fields = [] ) {
		$args = [
			'id'                 => get_the_ID(),
			'fields'             => $fields,
			'submit_value'       => __( 'Save Changes', 'rvrenopro' ),
			'return'             => '%post_url%',
			'html_submit_button' => '<input type="submit" class="button" value="%s" />',
			// 'updated_message' => '',
		];

		ob_start();
		acf_form( $args );
		$form = ob_get_clean();

		return $form;
	}

	/**
	 * Gets camera icon for image fields.
	 *
	 * @return string
	 */
	function get_camera_icon() {
		$icon = null;

		if ( ! is_null( $icon ) ) {
			return $icon;
		}

		$icon = mai_get_icon(
			[
				'icon'          => 'camera',
				'style'         => 'regular',
				'size'          => '2em',
				'margin_bottom' => '0.5em',
			]
		);

		return $icon ?: '';
	}

	/**
	 * Adds button to submit new renovation.
	 *
	 * @return void
	 */
	function do_renovation_actions() {
		$url = add_query_arg( [
			'rvrp_renovation' => 'new',
		], get_permalink() );

		echo '<div id="my-renovations" class="has-xl-margin-top has-xl-margin-bottom has-xl-padding has-alt-background-color has-border-radius" style="text-align:center;">';

			printf( '<h3>%s</h3>', __( 'Welcome!', 'rvrenopro' ) );
			printf( '<p>%s<br />%s</p>', __( 'Ready to add a new RV renovation?' , 'rvrenopro' ), __( 'Click the button below to fill get started!' , 'rvrenopro' ) );
			printf( '<p><a href="%s" class="button">%s</a></p>', esc_url( $url ), __( 'Add New Renovation', 'rvrenopro' ) );

			$renovations = $this->get_renovations();

			if ( $renovations ) {
				echo '<table class="rvrp-renovation-table has-lg-margin-top">';
					echo '<tr>';
						echo '<th colspan="2">Renovations</th>';
					echo '</tr>';

					foreach ( $renovations as $post_id ) {
						$url = add_query_arg( [
							'rvrp_renovation' => $post_id,
						], get_permalink() );

						printf( '<tr><td><a href="%s">%s</a></td><td><a class="rvrp-edit-link" href="%s">%s</a></td></tr>', get_permalink( $post_id ), get_the_title( $post_id ), $url, __( 'Edit', 'rvrenopro' ) );
					}
				echo '</table>';
			}

		echo '</div>';
	}

	/**
	 * Gets a logged in renovators renovations.
	 *
	 * @return array
	 */
	function get_renovations() {
		$ids   = [];
		$query = new WP_Query(
			[
				'post_type'              => 'renovation',
				'posts_per_page'         => 100,
				'post_status'            => 'any',
				'author__in'             => (array) get_current_user_id(),
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			]
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$ids[] = get_the_ID();
			endwhile;
		}
		wp_reset_postdata();

		return $ids;
	}

	/**
	 * Adds pending label to pending renovations.
	 *
	 * @param string $title   The post title.
	 * @param int    $post_id The post ID.
	 *
	 * @return string The post title.
	 */
	function do_pending_renovations( $title, $post_id ) {
		if ( 'renovation' !== get_post_type( $post_id ) ) {
			return $title;
		}

		if ( 'pending' !== get_post_status( $post_id ) ) {
			return $title;
		}

		return sprintf( '[%s] %s', __( 'Pending Approval', 'rvrenopro' ), $title );
	}
}

/**
 * Instantiates the class if user has proper privelages.
 *
 * @return void
 */
add_action( 'template_redirect', function() {
	if ( ! rvrp_is_renovators_profile() ) {
		return;
	}

	new RVRP_Renovator_Edit();
});
