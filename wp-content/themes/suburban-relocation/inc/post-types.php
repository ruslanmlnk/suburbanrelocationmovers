<?php
/** Custom content types. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function srs_register_post_types() {
	register_post_type(
		'srs_service',
		array(
			'labels' => array(
				'name'          => __( 'Services', 'suburban-relocation' ),
				'singular_name' => __( 'Service', 'suburban-relocation' ),
				'add_new_item'  => __( 'Add new service', 'suburban-relocation' ),
				'edit_item'     => __( 'Edit service article', 'suburban-relocation' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-admin-tools',
			'has_archive'  => 'services',
			'rewrite'      => array( 'slug' => 'service', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'template'     => array(
				array( 'core/paragraph', array( 'placeholder' => __( 'Write the service introduction…', 'suburban-relocation' ) ) ),
				array( 'core/heading', array( 'level' => 2, 'placeholder' => __( 'Section heading', 'suburban-relocation' ) ) ),
				array( 'core/paragraph', array( 'placeholder' => __( 'Add detailed service information…', 'suburban-relocation' ) ) ),
			),
		)
	);

	register_post_type(
		'srs_location',
		array(
			'labels' => array(
				'name'          => __( 'Locations', 'suburban-relocation' ),
				'singular_name' => __( 'Location', 'suburban-relocation' ),
				'add_new_item'  => __( 'Add new location', 'suburban-relocation' ),
				'edit_item'     => __( 'Edit location article', 'suburban-relocation' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-location-alt',
			'has_archive'  => 'locations',
			'rewrite'      => array( 'slug' => 'location', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'template'     => array(
				array( 'core/paragraph', array( 'placeholder' => __( 'Introduce this service area…', 'suburban-relocation' ) ) ),
				array( 'core/heading', array( 'level' => 2, 'placeholder' => __( 'Moving services in this area', 'suburban-relocation' ) ) ),
				array( 'core/paragraph', array( 'placeholder' => __( 'Add local details, routes and helpful information…', 'suburban-relocation' ) ) ),
			),
		)
	);

	register_post_type(
		'srs_lead',
		array(
			'labels' => array(
				'name'          => __( 'Quote Requests', 'suburban-relocation' ),
				'singular_name' => __( 'Quote Request', 'suburban-relocation' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt2',
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'supports'            => array( 'title' ),
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'srs_register_post_types' );

/** Helpful placeholders in the admin list. */
function srs_post_updated_messages( $messages ) {
	$messages['srs_service'][1]  = __( 'Service updated.', 'suburban-relocation' );
	$messages['srs_location'][1] = __( 'Location updated.', 'suburban-relocation' );
	return $messages;
}
add_filter( 'post_updated_messages', 'srs_post_updated_messages' );
