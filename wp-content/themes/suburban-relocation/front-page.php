<?php
/** Stable section-based front page. */
get_header();
$phone = srs_get_option( 'phone', '800-816-6834' );
$locations = get_posts(
	array(
		'post_type'      => 'srs_location',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'meta_key'       => '_srs_featured',
		'meta_value'     => '1',
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'order'          => 'ASC',
	)
);
?>
<main id="main-content" class="srs-front-page">
	<section class="srs-home-hero" id="top" style="--srs-hero-image:url('<?php echo esc_url( srs_get_home_option( 'hero_image' ) ); ?>')">
		<div class="srs-home-shade" aria-hidden="true"></div>
		<div class="srs-container srs-home-hero-grid">
			<div class="srs-hero-copy">
				<p class="srs-eyebrow"><?php echo esc_html( srs_get_home_option( 'hero_kicker' ) ); ?></p>
				<h1><?php echo esc_html( srs_get_home_option( 'hero_title' ) ); ?></h1>
				<p class="srs-hero-lead"><?php echo esc_html( srs_get_home_option( 'hero_text' ) ); ?></p>
				<div class="srs-hero-actions"><a class="srs-button" href="#quote"><?php echo esc_html( srs_get_home_option( 'hero_button' ) ); ?> <?php echo srs_icon( 'arrow-right' ); ?></a><a class="srs-phone-link" href="tel:<?php echo esc_attr( srs_phone_href( $phone ) ); ?>"><span class="srs-phone-icon"><?php echo srs_icon( 'phone' ); ?></span><span><small><?php echo esc_html( srs_get_home_option( 'hero_phone_label' ) ); ?></small><strong><?php echo esc_html( $phone ); ?></strong></span></a></div>
			</div>
			<div class="srs-hero-form"><?php echo srs_quote_form_shortcode( array( 'kicker' => srs_get_home_option( 'form_kicker' ), 'title' => srs_get_home_option( 'form_title' ), 'text' => srs_get_home_option( 'form_text' ), 'button' => srs_get_home_option( 'form_button' ) ) ); ?></div>
		</div>
	</section>

	<?php if ( '1' === srs_get_home_option( 'trust_enabled' ) ) : ?>
		<div class="srs-trust-wrap"><div class="srs-container srs-trust-strip"><?php $trust_icons = array( 'package-check', 'shield-check', 'headphones' ); for ( $i = 1; $i <= 3; $i++ ) : ?><div><span class="srs-trust-icon"><?php echo srs_icon( $trust_icons[ $i - 1 ] ); ?></span><span><strong><?php echo esc_html( srs_get_home_option( 'trust_' . $i . '_title' ) ); ?></strong><small><?php echo esc_html( srs_get_home_option( 'trust_' . $i . '_text' ) ); ?></small></span></div><?php endfor; ?></div></div>
	<?php endif; ?>

	<?php if ( '1' === srs_get_home_option( 'services_enabled' ) ) : ?>
		<section class="srs-section srs-services-section" id="services"><div class="srs-container"><div class="srs-section-head"><div><p class="srs-eyebrow dark"><?php echo esc_html( srs_get_home_option( 'services_kicker' ) ); ?></p><h2><?php echo esc_html( srs_get_home_option( 'services_title' ) ); ?></h2></div><p><?php echo esc_html( srs_get_home_option( 'services_text' ) ); ?></p></div><?php echo srs_services_grid_shortcode( array( 'count' => '4' ) ); ?></div></section>
	<?php endif; ?>

	<?php if ( '1' === srs_get_home_option( 'process_enabled' ) ) : ?>
		<section class="srs-section srs-process-section" id="process"><div class="srs-container srs-process-grid"><div class="srs-process-copy"><p class="srs-eyebrow"><?php echo esc_html( srs_get_home_option( 'process_kicker' ) ); ?></p><h2><?php echo esc_html( srs_get_home_option( 'process_title' ) ); ?></h2><p><?php echo esc_html( srs_get_home_option( 'process_text' ) ); ?></p><a class="srs-outline-button" href="#quote"><?php echo esc_html( srs_get_home_option( 'process_button' ) ); ?> <?php echo srs_icon( 'arrow-right' ); ?></a></div><ol class="srs-steps"><?php for ( $i = 1; $i <= 3; $i++ ) : ?><li><span><?php echo esc_html( $i ); ?></span><div><h3><?php echo esc_html( srs_get_home_option( 'step_' . $i . '_title' ) ); ?></h3><p><?php echo esc_html( srs_get_home_option( 'step_' . $i . '_text' ) ); ?></p></div></li><?php endfor; ?></ol></div></section>
	<?php endif; ?>

	<?php if ( '1' === srs_get_home_option( 'locations_enabled' ) ) : ?>
		<section class="srs-section srs-locations-section" id="locations"><div class="srs-container srs-home-location-grid"><div class="srs-map-panel" aria-hidden="true"><span class="srs-map-orbit one"></span><span class="srs-map-orbit two"></span><span class="srs-map-dot one"><i></i> <?php echo esc_html( srs_get_home_option( 'locations_map_1' ) ); ?></span><span class="srs-map-dot two"><i></i> <?php echo esc_html( srs_get_home_option( 'locations_map_2' ) ); ?></span><span class="srs-map-dot three"><i></i> <?php echo esc_html( srs_get_home_option( 'locations_map_3' ) ); ?></span><span class="srs-map-center"><?php echo srs_icon( 'truck' ); ?></span></div><div class="srs-location-copy"><p class="srs-eyebrow dark"><?php echo esc_html( srs_get_home_option( 'locations_kicker' ) ); ?></p><h2><?php echo esc_html( srs_get_home_option( 'locations_title' ) ); ?></h2><p><?php echo esc_html( srs_get_home_option( 'locations_text' ) ); ?></p><ul><li><?php echo srs_icon( 'check' ); ?> <?php echo esc_html( srs_get_home_option( 'locations_area_1' ) ); ?></li><li><?php echo srs_icon( 'check' ); ?> <?php echo esc_html( srs_get_home_option( 'locations_area_2' ) ); ?></li><li><?php echo srs_icon( 'check' ); ?> <?php echo esc_html( srs_get_home_option( 'locations_area_3' ) ); ?></li><li><?php echo srs_icon( 'check' ); ?> <?php echo esc_html( srs_get_home_option( 'locations_area_4' ) ); ?></li></ul><a class="srs-text-link" href="#quote"><?php echo esc_html( srs_get_home_option( 'locations_button' ) ); ?> <?php echo srs_icon( 'arrow-right' ); ?></a></div></div></section>
	<?php endif; ?>

	<?php if ( '1' === srs_get_home_option( 'reviews_enabled' ) ) : ?>
		<section class="srs-section srs-reviews-section" id="reviews"><div class="srs-container srs-review-layout"><div class="srs-review-intro"><p class="srs-eyebrow"><?php echo esc_html( srs_get_home_option( 'reviews_kicker' ) ); ?></p><h2><?php echo esc_html( srs_get_home_option( 'reviews_title' ) ); ?></h2><div class="srs-rating"><strong><?php echo esc_html( srs_get_home_option( 'reviews_rating' ) ); ?></strong><span>★★★★★<small><?php echo esc_html( srs_get_home_option( 'reviews_caption' ) ); ?></small></span></div></div><div class="srs-review-slider" data-review-slider><?php for ( $i = 1; $i <= 3; $i++ ) : ?><article class="srs-review-slide<?php echo 1 === $i ? ' is-active' : ''; ?>" data-review-slide><span class="srs-quote-mark"><?php echo srs_icon( 'sparkles' ); ?></span><blockquote>“<?php echo esc_html( srs_get_home_option( 'review_' . $i . '_quote' ) ); ?>”</blockquote><footer><span><strong><?php echo esc_html( srs_get_home_option( 'review_' . $i . '_name' ) ); ?></strong><small><?php echo esc_html( srs_get_home_option( 'review_' . $i . '_detail' ) ); ?></small></span><span class="srs-review-controls"><button type="button" data-review-prev aria-label="Previous review"><?php echo srs_icon( 'arrow-left' ); ?></button><em data-review-count><?php echo esc_html( $i . ' / 3' ); ?></em><button type="button" data-review-next aria-label="Next review"><?php echo srs_icon( 'arrow-right' ); ?></button></span></footer></article><?php endfor; ?></div></div></section>
	<?php endif; ?>

	<?php if ( '1' === srs_get_home_option( 'faq_enabled' ) ) : ?>
		<section class="srs-section srs-faq-section"><div class="srs-container srs-faq-grid"><div><p class="srs-eyebrow dark"><?php echo esc_html( srs_get_home_option( 'faq_kicker' ) ); ?></p><h2><?php echo esc_html( srs_get_home_option( 'faq_title' ) ); ?></h2><p><?php echo esc_html( srs_get_home_option( 'faq_text' ) ); ?></p><a class="srs-text-link" href="tel:<?php echo esc_attr( srs_phone_href( $phone ) ); ?>"><?php echo esc_html( srs_get_home_option( 'faq_call_label' ) . ' ' . $phone ); ?> →</a></div><div class="srs-faq-list"><?php for ( $i = 1; $i <= 3; $i++ ) : ?><details<?php echo 1 === $i ? ' open' : ''; ?>><summary><?php echo esc_html( srs_get_home_option( 'faq_' . $i . '_question' ) ); ?></summary><p><?php echo esc_html( srs_get_home_option( 'faq_' . $i . '_answer' ) ); ?></p></details><?php endfor; ?></div></div></section>
	<?php endif; ?>

	<?php if ( '1' === srs_get_home_option( 'cta_enabled' ) ) : ?>
		<section class="srs-final-cta"><div class="srs-container srs-final-cta-inner"><div><p class="srs-eyebrow"><?php echo esc_html( srs_get_home_option( 'cta_kicker' ) ); ?></p><h2><?php echo esc_html( srs_get_home_option( 'cta_title' ) ); ?></h2></div><div><a class="srs-cta-button" href="#quote"><?php echo esc_html( srs_get_home_option( 'cta_button' ) ); ?> <?php echo srs_icon( 'arrow-right' ); ?></a><a class="srs-cta-phone" href="tel:<?php echo esc_attr( srs_phone_href( $phone ) ); ?>"><?php echo srs_icon( 'phone' ); ?> <?php echo esc_html( $phone ); ?></a></div></div></section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
