<?php
/** Reusable editor patterns. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function srs_register_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}
	register_block_pattern_category( 'srs-sections', array( 'label' => __( 'Suburban Relocation Sections', 'suburban-relocation' ) ) );

	register_block_pattern(
		'suburban-relocation/article-section',
		array(
			'title'      => __( 'Article section with image', 'suburban-relocation' ),
			'categories' => array( 'srs-sections' ),
			'content'    => '<!-- wp:heading --><h2 class="wp-block-heading">A clear section heading</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Add detailed, helpful information here. You can insert more paragraphs, lists, galleries, video or any other Gutenberg block.</p><!-- /wp:paragraph --><!-- wp:image {"sizeSlug":"large"} --><figure class="wp-block-image size-large"><img src="' . esc_url( get_theme_file_uri( 'assets/images/residential-moving.webp' ) ) . '" alt="Professional moving service"/></figure><!-- /wp:image -->',
		)
	);

	register_block_pattern(
		'suburban-relocation/quote-cta',
		array(
			'title'      => __( 'Quote call to action', 'suburban-relocation' ),
			'categories' => array( 'srs-sections' ),
			'content'    => '<!-- wp:group {"className":"srs-final-cta","layout":{"type":"constrained"}} --><div class="wp-block-group srs-final-cta"><!-- wp:columns {"verticalAlignment":"center","className":"srs-container"} --><div class="wp-block-columns are-vertically-aligned-center srs-container"><!-- wp:column {"verticalAlignment":"center","width":"70%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:70%"><!-- wp:paragraph {"className":"srs-eyebrow"} --><p class="srs-eyebrow">Ready when you are</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Let’s make your next move feel lighter.</h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"30%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:30%"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#quote">Get a free quote</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->',
		)
	);
}
add_action( 'init', 'srs_register_patterns' );
