<?php
/** One-time migration of archived copy and locally stored content images. */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SRS_LEGACY_CONTENT_VERSION', '2026-09-05-2' );

function srs_import_archived_content() {
	if ( SRS_LEGACY_CONTENT_VERSION === get_option( 'srs_legacy_content_version' ) ) return;
	$file = get_template_directory() . '/inc/legacy-content.json';
	if ( ! is_readable( $file ) ) return;
	$dataset = json_decode( file_get_contents( $file ), true );
	if ( ! is_array( $dataset ) ) return;

	$state_slugs = array( 'maryland', 'washington-dc', 'virginia', 'colorado', 'california', 'texas' );
	foreach ( $dataset as $slug => $entry ) {
		/* The archive genuinely contained no article for these entries; retain the existing useful copy. */
		if ( empty( $entry['content'] ) || (int) ( $entry['word_count'] ?? 0 ) < 40 ) continue;
		if ( 'srs_service' === ( $entry['type'] ?? '' ) ) {
			$post_type = 'srs_service';
		} else {
			$post_type = in_array( $slug, $state_slugs, true ) ? 'srs_location' : 'srs_city';
		}
		$post = get_page_by_path( $slug, OBJECT, $post_type );
		if ( ! $post && 'srs_city' === $post_type ) $post = get_page_by_path( $slug, OBJECT, 'srs_location' );
		if ( ! $post ) continue;
		$content = str_replace(
			array( '{{theme_uri}}', '%7B%7Btheme_uri%7D%7D', '%7b%7btheme_uri%7d%7d' ),
			get_template_directory_uri(),
			$entry['content']
		);
		wp_update_post( array( 'ID' => $post->ID, 'post_type' => $post_type, 'post_content' => wp_kses_post( $content ) ) );
		update_post_meta( $post->ID, '_srs_legacy_source_url', esc_url_raw( $entry['source'] ?? '' ) );
		update_post_meta( $post->ID, '_srs_legacy_url', sanitize_text_field( $entry['legacy_url'] ?? '' ) );
		if ( ! empty( $entry['meta_description'] ) ) update_post_meta( $post->ID, '_srs_meta_description', sanitize_text_field( $entry['meta_description'] ) );
	}
	update_option( 'srs_legacy_content_version', SRS_LEGACY_CONTENT_VERSION );
}
add_action( 'init', 'srs_import_archived_content', 60 );

function srs_archived_meta_description() {
	if ( ! is_singular( array( 'srs_service', 'srs_location', 'srs_city' ) ) || defined( 'WPSEO_VERSION' ) ) return;
	$description = get_post_meta( get_queried_object_id(), '_srs_meta_description', true );
	if ( $description ) echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'srs_archived_meta_description', 2 );
