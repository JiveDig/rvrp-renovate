<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Renovator profile class.
 */
class RVRP_Renovation {
	protected $user_id;
	protected $profile_id;
	protected $profile_url;

	/**
	 * Constructs the class.
	 *
	 * @return void
	 */
	function __construct() {
		$this->user_id     = get_post_field( 'post_author', get_the_ID() );
		$profile           = function_exists( 'maiup_get_user_post' ) ? maiup_get_user_post( $this->user_id ) : 0;
		$this->profile_id  = $profile ? $profile->ID : 0;
		$this->profile_url = get_permalink( $this->profile_id );
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
		// Add avatar.
		add_action( 'mai_after_entry_image', [ $this, 'do_avatar' ], 10, 2 );
		// Add renovator info.
		add_action( 'mai_after_entry_image', [ $this, 'do_renovator' ], 10, 2 );
		// Add content.
		add_action( 'genesis_entry_content', [ $this, 'do_content' ], 12 );
	}

	/**
	 * Enqueues CSS files.
	 *
	 * @return void
	 */
	function enqueue() {
		$file      = 'assets/css/renovation.css';
		$version   = RVRP_RENOVATE_VERSION;
		$file_path = RVRP_RENOVATE_PLUGIN_DIR . $file;
		$file_url  = RVRP_RENOVATE_PLUGIN_URL . $file;

		if ( file_exists( $file_path ) ) {
			$version .= '.' . date( 'njYHi', filemtime( $file_path ) );
			wp_enqueue_style( 'rvrp-renovation', $file_url, [], $version );
		}
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
		if ( 'renovation' !== $entry->post_type ) {
			return;
		}

		$avatar   = '';
		$image_id = $this->get_avatar_id();

		if ( $image_id ) {
			$avatar .= '<div class="rvrp-avatar">';
				$avatar .= sprintf( '<a href="%s" class="rvrp-avatar__link">', $this->profile_url );
					$avatar .= wp_get_attachment_image( $image_id, 'square-sm', false, [ 'class' => 'rvrp-avatar__image' ] );
				$avatar .= '</a>';
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

		$image_id  = get_post_meta( $this->profile_id, 'avatar_id', true );
		$image_id  = $image_id ?: rvrp_get_avatar_fallback();

		return $image_id;
	}

	/**
	 * Displays renovator info.
	 *
	 * @param WP_Post $entry The post object.
	 * @param array   $args  The markup args.
	 *
	 * @return void
	 */
	function do_renovator( $entry, $args ) {
		if ( ! isset( $args['context'] ) || 'single' !== $args['context'] ) {
			return;
		}

		echo '<p style="margin-top:calc(var(--spacing-md) * -1);margin-bottom:var(--spacing-xl);text-align:center;">';
			printf( '%s: <strong><a href="%s">%s</a></strong>', __( 'Renovated by', 'rvrenopro' ), $this->profile_url, get_the_title( $this->profile_id ) );

			if ( rvrp_is_renovators_renovation() ) {
				if ( $this->profile_url ) {
					$url = add_query_arg( [
						'rvrp_renovation' => get_the_ID(),
					], $this->profile_url );

					printf( ' | <a class="rvrp-edit-link" href="%s">[%s]</a>', esc_url( $url ), __( 'Edit Renovation', 'rvrenopro' ) );
				}
			}
		echo '</p>';
	}

	/**
	 * Displays content from custom fields.
	 *
	 * @return void
	 */
	function do_content() {
		$year  = get_field( 'rvrp_year' );
		$make  = get_field( 'rvrp_make' );
		$model = get_field( 'rvrp_model' );
		$types = get_the_terms( get_the_ID(), 'renovation_type' );
		$types = $types && ! is_wp_error( $types ) ? wp_list_pluck( $types, 'name' ) : [];
		$types = $types ? implode( ', ', $types ) : '';


		echo '<div class="rvrp-columns rvrp-general has-xxl-margin-bottom">';
			echo '<div class="rvrp-column">';
				printf( '<h2>%s</h2>', __( 'Year', 'rvrenopro' ) );
				echo $year ?: 'n/a';
			echo '</div>';
			echo '<div class="rvrp-column">';
				printf( '<h2>%s</h2>', __( 'Make', 'rvrenopro' ) );
				echo $make ?: 'n/a';
			echo '</div>';
			echo '<div class="rvrp-column">';
				printf( '<h2>%s</h2>', __( 'Model', 'rvrenopro' ) );
				echo $model ?: 'n/a';
			echo '</div>';
			echo '<div class="rvrp-column">';
				printf( '<h2>%s</h2>', __( 'Types', 'rvrenopro' ) );
				echo $types ?: 'n/a';
			echo '</div>';
		echo '</div>';

		// Get other fields.
		$fields = acf_get_fields( 'group_63602dbf9a7d1' );
		$labels = wp_list_pluck( $fields, 'label', 'name' );
		$skips  = [
			'_thumbnail_id',
			'rvrp_adjective',
			'rvrp_year',
			'rvrp_make',
			'rvrp_model',
			'rvrp_type',
		];

		foreach ( $fields as $field ) {
			if ( in_array( $field['name'], $skips ) ) {
				continue;
			}

			switch ( $field['type'] ) {
				case 'message':
					echo $field['message'];
					echo '<div class="has-md-padding"></div>';
				case 'number':
				case 'text':
				case 'textarea':
				case 'wysiwyg':
					$value = get_field( $field['name'] );

					if ( ! is_null( $value ) && '' !== $value ) {
						printf( '<h3 class="has-lg-font-size">%s</h3>', $field['label'] );
						echo mai_get_processed_content( wp_kses_post( $value ) );
						echo '<div class="has-lg-padding"></div>';
					}
				break;
				case 'gallery':
					$value = get_field( $field['name'] );

					if ( $value && function_exists( 'mai_get_gallery' ) ) {
						printf( '<h3 class="has-lg-font-size">%s</h3>', $field['label'] );
						echo mai_get_gallery(
							[
								'images'             => array_map( 'absint', $value ),
								'image_orientation'  => 'square',
								'lightbox'           => true,
								'columns'            => 3,
								'columns_responsive' => true,
								'columns_md'         => 3,
								'columns_sm'         => 2,
								'columns_xs'         => 2,
								'column_gap'         => 'md',
								'row_gap'            => 'md',
							]
						);
						echo '<div class="has-lg-padding"></div>';
					}
				break;
			}
		}

		$keys   = [
			// 'rvrp_adjective',
			// 'rvrp_year',
			// 'rvrp_make',
			// 'rvrp_model',
			'rvrp_slideouts',
			'rvrp_sleep',
			'rvrp_acquisition',
			'rvrp_attraction',
			'rvrp_damage',
			// 'rvrp_before_photos',
			'rvrp_improvements',
			'rvrp_time',
			'rvrp_cost',
			'rvrp_difference',
			'rvrp_challange',
			'rvrp_proud',
			'rvrp_style',
			'rvrp_influences',
			// 'rvrp_after_photos',
			'rvrp_paint',
			'rvrp_materials',
			'rvrp_shop',
		];

		// $headings = [
		// ];
		// $array = [
		// 	__( 'About this RV', 'rvrenopro' ) = [
		// 		'rvrp_slideouts',
		// 		'rvrp_sleep',
		// 		'rvrp_acquisition',
		// 		'rvrp_attraction',
		// 		'rvrp_damage',
		// 	],
		// 	__( 'About the process', 'rvrenopro' ) => [
		// 		'rvrp_improvements',
		// 		'rvrp_time',
		// 		'rvrp_cost',
		// 		'rvrp_difference',
		// 		'rvrp_challange',
		// 		'rvrp_proud',
		// 		'rvrp_style',
		// 		'rvrp_influences',
		// 	],
		// 	__( 'Resources', 'rvrenopro' ) => [
		// 		'rvrp_paint',
		// 		'rvrp_materials',
		// 		'rvrp_shop',
		// 	],
		// ];


		// foreach ( $array as $heading => $keys ) {

		// 	printf( '<h2 class="has-xl-margin-bottom">%s</h2>', $heading );

		// 	foreach ( $keys as $key ) {
		// 		$label = $labels[ $key ];
		// 		$value = get_field( $key );

		// 		if ( is_null( $value ) || '' == $value ) {
		// 			continue;
		// 		}

		// 		printf( '<h3 class="has-md-font-size">%s</h3>', $label );
		// 		echo $value;
		// 		echo '<div class="has-xl-padding"></div>';
		// 	}
		// }
	}
}

/**
 * Instantiates the class.
 *
 * @return void
 */
add_action( 'template_redirect', function() {
	if ( ! is_singular( 'renovation' ) ) {
		return;
	}

	new RVRP_Renovation;
});
