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
});
