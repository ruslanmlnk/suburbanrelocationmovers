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
				'name'          => __( 'States', 'suburban-relocation' ),
				'singular_name' => __( 'State', 'suburban-relocation' ),
				'add_new_item'  => __( 'Add new state', 'suburban-relocation' ),
				'edit_item'     => __( 'Edit state article', 'suburban-relocation' ),
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
		'srs_city',
		array(
			'labels' => array(
				'name'          => __( 'Cities', 'suburban-relocation' ),
				'singular_name' => __( 'City', 'suburban-relocation' ),
				'add_new_item'  => __( 'Add new city', 'suburban-relocation' ),
				'edit_item'     => __( 'Edit city article', 'suburban-relocation' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-building',
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'city', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'template'     => array(
				array( 'core/paragraph', array( 'placeholder' => __( 'Introduce moving services in this city…', 'suburban-relocation' ) ) ),
				array( 'core/heading', array( 'level' => 2, 'placeholder' => __( 'Local moving services', 'suburban-relocation' ) ) ),
				array( 'core/paragraph', array( 'placeholder' => __( 'Add local routes, neighborhoods and helpful information…', 'suburban-relocation' ) ) ),
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

/** Default state relationships for the historic city URLs imported with the theme. */
function srs_city_state_slug_map() {
	return array(
		'colorado' => array( 'applewood', 'aspen-park', 'aurora', 'boulder', 'brighton', 'broomfield', 'castle-rock', 'centennial', 'colorado-springs', 'commerce-city', 'conifer', 'denver', 'edgewater', 'englewood', 'evergreen', 'fort-collins', 'golden', 'greeley', 'highlands-ranch', 'lafayette', 'lakewood', 'littleton', 'longmont', 'louisville', 'loveland', 'monument', 'northglenn-henderson', 'parker', 'thornton', 'wellington', 'westminster', 'woodland-park', 'woodmoor' ),
		'california' => array( 'beverly-hills', 'hollywood', 'irvine', 'los-angeles' ),
		'maryland' => array( 'annapolis', 'baltimore', 'bethesda', 'chevy-chase', 'college-park', 'columbia-md', 'derwood', 'gaithersburg', 'garrett-park', 'greenbelt', 'hyattsville', 'kensington', 'laurel-md', 'montgomery', 'olney', 'potomac', 'rockville', 'sandy-spring', 'silver-spring', 'towson', 'upper-marlboro' ),
		'virginia' => array( 'alexandria', 'arlington', 'ashburn', 'centreville', 'chester', 'fairfax', 'laurel-va', 'lorton', 'manassas', 'mclean', 'newport-news', 'norfolk', 'springfield', 'sterling', 'vienna', 'woodbridge' ),
	);
}

function srs_state_slug_for_city( $city_slug ) {
	foreach ( srs_city_state_slug_map() as $state_slug => $cities ) {
		if ( in_array( $city_slug, $cities, true ) ) return $state_slug;
	}
	return '';
}

/** Split legacy mixed Locations into separate States and Cities once per installation. */
function srs_migrate_cities_to_post_type() {
	if ( '1' === get_option( 'srs_city_structure_version' ) ) return;
	$state_slugs = array( 'maryland', 'washington-dc', 'virginia', 'colorado', 'california', 'texas' );
	$states = get_posts( array( 'post_type' => 'srs_location', 'post_status' => 'any', 'posts_per_page' => -1 ) );
	$state_ids = array();
	foreach ( $states as $state ) if ( in_array( $state->post_name, $state_slugs, true ) ) $state_ids[ $state->post_name ] = $state->ID;
	foreach ( $states as $post ) {
		if ( in_array( $post->post_name, $state_slugs, true ) ) continue;
		wp_update_post( array( 'ID' => $post->ID, 'post_type' => 'srs_city' ) );
		$state_slug = srs_state_slug_for_city( $post->post_name );
		if ( $state_slug && isset( $state_ids[ $state_slug ] ) ) update_post_meta( $post->ID, '_srs_parent_state', $state_ids[ $state_slug ] );
	}
	update_option( 'srs_city_structure_version', '1' );
	flush_rewrite_rules( false );
}
add_action( 'init', 'srs_migrate_cities_to_post_type', 40 );

/** Helpful placeholders in the admin list. */
function srs_post_updated_messages( $messages ) {
	$messages['srs_service'][1]  = __( 'Service updated.', 'suburban-relocation' );
	$messages['srs_location'][1] = __( 'Location updated.', 'suburban-relocation' );
	$messages['srs_city'][1]     = __( 'City updated.', 'suburban-relocation' );
	return $messages;
}
add_filter( 'post_updated_messages', 'srs_post_updated_messages' );
