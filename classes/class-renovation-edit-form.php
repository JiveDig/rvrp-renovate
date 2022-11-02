<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Renovation new and edit form.
 */
class RVRP_Renovation_Edit_Form {
	protected $post_id;
	protected $new;

	/**
	 * Constructs the class.
	 *
	 * @return void
	 */
	function __construct( $post_id ) {
		$this->post_id = $post_id; // Sanitized in listener class.
		$this->new     = 'new' === $this->post_id;
		$this->hooks();
	}

	/**
	 * Runs hooks.
	 *
	 * @return void
	 */
	function hooks() {
		add_action( 'acf/save_post', [ $this, 'save_post' ], 20 );
		add_action( 'genesis_loop',  [ $this, 'add_form' ] );
	}

	/**
	 * Processes form submission and creates a new post.
	 *
	 * @return void
	 */
	function save_post( $post_id ) {
		$adjective = get_field( 'rvrp_adjective', $post_id );
		$year      = get_field( 'rvrp_year', $post_id );
		$make      = get_field( 'rvrp_make', $post_id );
		$model     = get_field( 'rvrp_model', $post_id );
		$title     = trim( sprintf( '%s %s %s %s', esc_html( $adjective ), esc_html( $year ), esc_html( $make ), esc_html( $model ) ) );

		$args = [
			'ID'         => $post_id,
			'post_title' => $title,
		];

		if ( $this->new ) {
			$args['post_name'] = $title; // Sanitized inside `wp_update_post()` function.
		}

		wp_update_post( $args );

		if ( $this->new ) {
			$this->send_email();
		}
	}

	/**
	 * Sends email notification for new revovator subsmissions.
	 *
	 * @return void
	 */
	function send_email() {
		$emails = [
			'allthingswithpurpose@gmail.com',
			'mike@thestizmedia.com',
		];

		$subject = __( 'New RV Renovation!', 'rvrenopro' ) . ' -- ' . get_the_title( $post_id );
		$body    = __( 'View: ', 'rvrenopro' ) . get_permalink( $post_id );
		$body   .= "\n\n";
		$body   .= __( 'Edit/Approve: ', 'rvrenopro' ) . get_edit_post_link( $post_id );

		wp_mail( $emails, $subject, $body );
	}

	/**
	 * Adds renovation submission form.
	 *
	 * @return void
	 */
	function add_form() {
		$title    = $this->new ? __( 'RV Renovation Submission', 'rvrenopro' ) : __( 'Edit', 'rvrenopro' ) . ' ' . get_the_title( $this->post_id );
		$submit   = $this->new ? __( 'Submit Renovation', 'rvrenopro' ) : __( 'Save Changes', 'rvrenopro' );
		$updated  = $this->new ? __( 'Thank you for submitting your renovation! We will email you once your renovation has been approved for display.', 'rvrenopro' ) : __( 'Changes saved successfully.', 'rvrenopro' );
		$post     = function_exists( 'maiup_get_user_post' ) ? maiup_get_user_post( get_current_user_id() ) : false;
		$redirect = $post ? get_permalink( $post->ID ) . '#my-renovations' : '';

		printf( '<p class="has-lg-margin-bottom"><a href="%s">← %s</a></p>', esc_url( $redirect ), __( 'Back to my profile', 'rvrenopro' ) );
		printf( '<h1>%s</h1>', $title );

		$args = [
			'field_groups'       => [ 'group_63602dbf9a7d1' ],
			'post_id'            => $this->new ? 'new_post' : $this->post_id,
			'submit_value'       => $submit,
			'updated_message'    => __( 'Thank you for submitting your renovation! We will email you once your renovation has been approved for display.', 'rvrenopro' ),
			'html_submit_button' => '<input type="submit" class="button" value="%s" />',
			'return'             => esc_url( $redirect ),
		];

		if ( $this->new ) {
			$args['new_post'] = [
				'post_type'   => 'renovation',
				'post_status' => 'pending',
			];
		}

		acf_form( $args );

		if ( $this->new ) {
			printf( '<p class="has-xl-margin-top has-xs-font-size" style="text-align:center;"><em>%s</em></p>', __( 'By submitting this renovation you give RV Reno Pro the right to post publicly on social media and other platforms, and tastefully edit any answers or content.', 'rvrenopro' ) );
		}
	}
}
