<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Renovator profile edit class.
 */
class RVRP_Edit_Listener {
	protected $post_id;

	/**
	 * Constructs the class.
	 *
	 * @return void
	 */
	function __construct() {
		$run           = false;
		$renovator_id  = filter_input( INPUT_GET, 'rvrp_renovator', FILTER_VALIDATE_INT );
		$renovation_id = filter_input( INPUT_GET, 'rvrp_renovation', FILTER_SANITIZE_STRING ); // 'new' for new post.
		$renovation_id = 'new' === $renovation_id ? $renovation_id : absint( $renovation_id );

		if ( ! ( $renovator_id || $renovation_id ) ) {
			return;
		}

		if ( $renovator_id && rvrp_is_renovators_profile( $renovator_id ) ) {
			$form = new RVRP_Renovator_Edit_Form( $renovator_id );
			$run  = true;
		} elseif ( $renovation_id && ( 'new' === $renovation_id || rvrp_is_renovators_renovation( $renovation_id ) ) ) {
			$form = new RVRP_Renovation_Edit_Form( $renovation_id );
			$run  = true;
		}

		if ( ! $run ) {
			return;
		}

		$this->hide_loop();
		$this->hooks();
	}

	/**
	 * Removes the Genesis/Mai loop.
	 *
	 * @return void
	 */
	function hide_loop() {
		add_action( 'genesis_before_loop', function() {
			remove_action( 'genesis_loop', 'genesis_do_loop' );
			remove_action( 'genesis_loop', 'mai_do_loop' );
		});
	}

	/**
	 * Runs hooks.
	 *
	 * @return void
	 */
	function hooks() {
		add_filter( 'genesis_site_layout', [ $this, 'site_layout' ], 99, 1 );
		add_filter( 'gettext',             [ $this, 'translate_text' ], 10, 3 );
		add_action( 'get_header',          [ $this, 'get_form_head' ] );
	}


	/**
	 * Sets site layout.
	 *
	 * @param string $layout The current site layout.
	 *
	 * @return string
	 */
	function site_layout( $layout ) {
		return 'narrow-content';
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
	 * Gets header code.
	 *
	 * @return void
	 */
	function get_form_head() {
		acf_form_head();
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

	new RVRP_Edit_Listener();
});
