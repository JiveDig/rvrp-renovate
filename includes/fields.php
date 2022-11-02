<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// Post data. Inactive and only used for front end editing.
	acf_add_local_field_group(
		[
			'key'    => 'rvrp_post_field_group',
			'title'  => __( 'Profile Post Data', 'rvrenopro' ),
			'fields' => [
				[
					'key'           => 'rvrp_post_title',
					'label'         => __( 'Name', 'rvrenopro' ),
					'name'          => 'post_title',
					'type'          => 'text',
				],
				[
					'key'           => 'rvrp_post_excerpt',
					'label'         => __( 'Bio (Short Description)', 'rvrenopro' ),
					'name'          => 'post_excerpt',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_post_content',
					'label'         => __( 'Story (Long Description)', 'rvrenopro' ),
					'name'          => 'post_content',
					'type'          => 'wysiwyg',
					'tabs'          => 'visual',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'delay'         => 0,
				],
				[
					'key'           => 'rvrp_post_featured_image_id',
					'label'         => __( 'Banner Photo', 'rvrenopro' ),
					'name'          => '_thumbnail_id',
					'aria-label'    => '',
					'type'          => 'image',
					'return_format' => 'id',
					'library'       => 'uploadedTo',
					'preview_size'  => 'landscape-md',
				],
				[
					'key'           => 'rvrp_post_account_type',
					'label'         => __( 'Account Type', 'rvrenopro' ),
					'name'          => 'type',
					'type'          => 'taxonomy',
					'taxonomy'      => 'renovator_type',
					'add_term'      => 0,
					'save_terms'    => 1,
					'load_terms'    => 1,
					'return_format' => 'id',
					'field_type'    => 'radio',
					'multiple'      => 0,
					'allow_null'    => 1,
				],
			],
			'location' => false,
			'active'   => false,
		]
	);

	// Profile photo in editor sidebar.
	acf_add_local_field_group(
		[
			'key'    => 'rvrp_post_sidebar_field_group',
			'title'  => __( 'Profile Photo', 'rvrenopro' ),
			'fields' => [
				[
					'key'           => 'rvrp_avatar_id',
					'label'         => __( 'Profile Photo', 'rvrenopro' ),
					'name'          => 'avatar_id',
					'type'          => 'image',
					'return_format' => 'id',
					'library'       => 'uploadedTo',
					'preview_size'  => is_admin() ? 'thumbnail' : 'square-sm',
				],
			],
			'location' => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'mai_user',
					],
				],
				[
					[
						'param'    => 'user_form',
						'operator' => '==',
						'value'    => 'edit',
					],
				],
			],
			'position' => 'side',
			'active'   => true,
		]
	);

	// Profile info at bottom of editor.
	acf_add_local_field_group(
		[
			'key'    => 'rvrp_post_info_field_group',
			'title'  => __( 'Profile Info', 'rvrenopro' ),
			'fields' => [
				[
					'key'     => 'rvrp_post_website',
					'label'   => __( 'Website (URL)', 'rvrenopro' ),
					'name'    => 'website',
					'type'    => 'url',
				],
				[
					'key'     => 'rvrp_post_instagram',
					'label'   => __( 'Instagram (handle)', 'rvrenopro' ),
					'name'    => 'instagram',
					'type'    => 'text',
					'prepend' => '@',
				],
				[
					'key'     => 'rvrp_post_twitter',
					'label'   => __( 'Twitter (handle)', 'rvrenopro' ),
					'name'    => 'twitter',
					'type'    => 'text',
					'prepend' => '@',
				],
				[
					'key'     => 'rvrp_post_youtube',
					'label'   => __( 'YouTube (URL)', 'rvrenopro' ),
					'name'    => 'youtube',
					'type'    => 'url',
				],
				[
					'key'     => 'rvrp_post_pinterest',
					'label'   => __( 'Pinterest (URL)', 'rvrenopro' ),
					'name'    => 'pinterest',
					'type'    => 'url',
				],
				[
					'key'     => 'rvrp_post_facebook',
					'label'   => __( 'Facebook (URL)', 'rvrenopro' ),
					'name'    => 'facebook',
					'type'    => 'url',
				],
			],
			'location' => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'mai_user',
					],
				],
				[
					[
						'param'    => 'user_form',
						'operator' => '==',
						'value'    => 'edit',
					],
				],

			],
			'active' => true,
		]
	);

	// Renovation Info.
	acf_add_local_field_group(
		[
			'key'    => 'rvrp_renovation_info_field_group',
			'title'  => __( 'Renovation Info', 'rvrenopro' ),
			'fields' => [
				[
					'key'           => 'rvrp_renovation_featured_image_id',
					'label'         => __( 'Featured / Main image', 'rvrenopro' ),
					'name'          => '_thumbnail_id',
					'type'          => 'image',
					'return_format' => 'id',
					'library'       => 'uploadedTo',
					'preview_size'  => 'landscape-md',
				],
				[
					'key'           => 'rvrp_renovation_rv_heading',
					'type'          => 'message',
					'message'       => __( '<h2>About the RV</h2>', 'rvrenopro' ),
					'new_lines'     => '',
					'esc_html'      => 0,
				],
				[
					'key'           => 'rvrp_renovation_adjective',
					'label'         => __( 'Adjective or descriptive word to describe this camper?', 'rvrenopro' ),
					'name'          => 'rvrp_adjective',
					'type'          => 'text',
					'instructions'  => __( 'This will be used in the main title', 'rvrenopro' ),
				],
				[
					'key'           => 'rvrp_63602e81ab92b',
					'label'         => __( 'Year', 'rvrenopro' ),
					'name'          => 'rvrp_year',
					'type'          => 'text',
					'required'      => 1,
					'wrapper'       => [
						'width'        => '33.333333',
					],
				],
				[
					'key'           => 'rvrp_renovation_make',
					'label'         => __( 'Make', 'rvrenopro' ),
					'name'          => 'rvrp_make',
					'type'          => 'text',
					'required'      => 1,
					'wrapper'       => [
						'width'        => '33.333333',
					],
				],
				[
					'key'           => 'rvrp_renovation_model',
					'label'         => __( 'Model', 'rvrenopro' ),
					'name'          => 'rvrp_model',
					'type'          => 'text',
					'required'      => 1,
					'wrapper'       => [
						'width'        => '33.333333',
					],
				],
				[
					'key'           => 'rvrp_renovation_type',
					'label'         => __( 'RV Type', 'rvrenopro' ),
					'name'          => 'rvrp_type',
					'type'          => 'taxonomy',
					'required'      => 1,
					'taxonomy'      => 'renovation_type',
					'add_term'      => 0,
					'save_terms'    => 1,
					'load_terms'    => 1,
					'return_format' => 'id',
					'field_type'    => 'checkbox',
					'multiple'      => 0,
					'allow_null'    => 0,
				],
				[
					'key'           => 'rvrp_renovation_slideouts',
					'label'         => __( 'Number of slideouts', 'rvrenopro' ),
					'name'          => 'rvrp_slideouts',
					'type'          => 'number',
					'default_value' => 0,
					'min'           => 0,
				],
				[
					'key'           => 'rvrp_renovation_sleep',
					'label'         => __( 'How many people does it sleep?', 'rvrenopro' ),
					'name'          => 'rvrp_sleep',
					'type'          => 'text',
				],
				[
					'key'           => 'rvrp_renovation_acquisition',
					'label'         => __( 'Where did you find/purchase this RV?', 'rvrenopro' ),
					'name'          => 'rvrp_acquisition',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_attraction',
					'label'         => __( 'What attracted you to this particular model?', 'rvrenopro' ),
					'name'          => 'rvrp_attraction',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_damage',
					'label'         => __( 'Was there damage you had to repair?', 'rvrenopro' ),
					'name'          => 'rvrp_damage',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_before_photos',
					'label'         => __( '"Before" photos', 'rvrenopro' ),
					'name'          => 'rvrp_before_photos',
					'type'          => 'gallery',
					'return_format' => 'id',
					'library'       => 'uploadedTo',
					'insert'        => 'append',
					'preview_size'  => 'square-sm',
				],
				[
					'key'           => 'rvrp_renovation_process_heading',
					'type'          => 'message',
					'message'       => __( '<h2>About the Process</h2>', 'rvrenopro' ),
					'new_lines'     => '',
					'esc_html'      => 0,
				],
				[
					'key'           => 'rvrp_renovation_improvements',
					'label'         => __( 'What were the main improvements done to this RV?', 'rvrenopro' ),
					'name'          => 'rvrp_improvements',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_time',
					'label'         => __( 'How long did the renovation process take?', 'rvrenopro' ),
					'name'          => 'rvrp_time',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_cost',
					'label'         => __( 'Approximately how much did the renovation cost?', 'rvrenopro' ),
					'name'          => 'rvrp_cost',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_difference',
					'label'         => __( 'What change(s] made the biggest difference?', 'rvrenopro' ),
					'name'          => 'rvrp_difference',
					'type'          => 'textarea',
				],
				[
					'key'           => 'rvrp_renovation_challange',
					'label'         => __( 'What was the biggest challange about this renovation?', 'rvrenopro' ),
					'name'          => 'rvrp_challange',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_proud',
					'label'         => __( 'What are you most proud of about this renovation?', 'rvrenopro' ),
					'name'          => 'rvrp_proud',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_style',
					'label'         => __( 'How would you describe the design style?', 'rvrenopro' ),
					'name'          => 'rvrp_style',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_influences',
					'label'         => __( 'What are your biggest style influences?', 'rvrenopro' ),
					'name'          => 'rvrp_influences',
					'type'          => 'textarea',
					'new_lines'     => '',
				],
				[
					'key'           => 'rvrp_renovation_after_photos',
					'label'         => __( '"After" photos', 'rvrenopro' ),
					'name'          => 'rvrp_after_photos',
					'type'          => 'gallery',
					'return_format' => 'id',
					'library'       => 'uploadedTo',
					'insert'        => 'append',
					'preview_size'  => 'square-sm',
				],
				[
					'key'           => 'rvrp_renovation_sources_heading',
					'type'          => 'message',
					'message'       => sprintf( '<h2>%s</h2><p>%s</p>', __( 'Sources', 'rvrenopro' ), __( 'Feel free to share any links to your favorite shops, products, businesses, etc.', 'rvrenopro' ) ),
					'new_lines'     => '',
					'esc_html'      => 0,
				],
				[
					'key'           => 'rvrp_renovation_paint',
					'label'         => __( 'What paint and colors did you use?', 'rvrenopro' ),
					'name'          => 'rvrp_paint',
					'type'          => 'wysiwyg',
					'tabs'          => 'visual',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'delay'         => 0,
				],
				[
					'key'           => 'rvrp_renovation_materials',
					'label'         => __( 'What materials, items, decor, etc. were used in this renovation?', 'rvrenopro' ),
					'name'          => 'rvrp_materials',
					'type'          => 'wysiwyg',
					'tabs'          => 'visual',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'delay'         => 0,
				],
				[
					'key'           => 'rvrp_renovation_shop',
					'label'         => __( 'Where is your favorite place to shop for renovation supplies?', 'rvrenopro' ),
					'name'          => 'rvrp_shop',
					'type'          => 'wysiwyg',
					'tabs'          => 'visual',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'delay'         => 0,
				],
			],
			'location' => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'renovation',
					],
				],
			],
			'active'       => true,
			'show_in_rest' => 0,
		],
	);
});
