<?php
/** Shared service/location article layout. */
$is_location = 'srs_location' === get_post_type();
$kicker      = srs_meta( '_srs_kicker', get_the_ID(), $is_location ? __( 'Local moving specialists', 'suburban-relocation' ) : __( 'Professional moving service', 'suburban-relocation' ) );
?>
<section class="srs-article-hero" style="--srs-article-image:url('<?php echo esc_url( srs_featured_image_url( get_the_ID(), 'full' ) ); ?>')">
	<div class="srs-article-shade"></div>
	<div class="srs-container srs-article-hero-inner">
		<nav class="srs-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'suburban-relocation' ); ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'suburban-relocation' ); ?></a><span>/</span><a href="<?php echo esc_url( get_post_type_archive_link( get_post_type() ) ); ?>"><?php echo esc_html( $is_location ? __( 'Locations', 'suburban-relocation' ) : __( 'Services', 'suburban-relocation' ) ); ?></a></nav>
		<p class="srs-eyebrow"><?php echo esc_html( $kicker ); ?></p>
		<h1><?php the_title(); ?></h1>
		<?php if ( has_excerpt() ) : ?><p class="srs-article-intro"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
	</div>
</section>
<section class="srs-article-section">
	<div class="srs-container srs-article-layout">
		<article class="srs-article-content">
			<?php the_content(); ?>
		</article>
		<aside class="srs-article-sidebar">
			<?php echo do_shortcode( '[srs_quote_form compact="yes" title="Your next move starts here" text="Share the basics and a moving coordinator will follow up."]' ); ?>
			<div class="srs-sidebar-contact">
				<small><?php esc_html_e( 'Talk to a moving expert', 'suburban-relocation' ); ?></small>
				<a href="tel:<?php echo esc_attr( srs_phone_href( $is_location ? srs_meta( '_srs_local_phone', get_the_ID(), srs_get_option( 'phone', '800-816-6834' ) ) : '' ) ); ?>"><?php echo esc_html( $is_location ? srs_meta( '_srs_local_phone', get_the_ID(), srs_get_option( 'phone', '800-816-6834' ) ) : srs_get_option( 'phone', '800-816-6834' ) ); ?></a>
				<?php if ( $is_location && srs_meta( '_srs_address' ) ) : ?><p><?php echo esc_html( srs_meta( '_srs_address' ) ); ?></p><?php endif; ?>
			</div>
		</aside>
	</div>
</section>
<section class="srs-benefits">
	<div class="srs-container">
		<div><strong><?php esc_html_e( 'Careful handling', 'suburban-relocation' ); ?></strong><small><?php esc_html_e( 'Protected from door to door', 'suburban-relocation' ); ?></small></div>
		<div><strong><?php esc_html_e( 'Professional crews', 'suburban-relocation' ); ?></strong><small><?php esc_html_e( 'Prepared for every move', 'suburban-relocation' ); ?></small></div>
		<div><strong><?php esc_html_e( 'Clear communication', 'suburban-relocation' ); ?></strong><small><?php esc_html_e( 'One team, regular updates', 'suburban-relocation' ); ?></small></div>
	</div>
</section>
<section class="srs-article-cta">
	<div class="srs-container"><div><p><?php esc_html_e( 'Ready when you are', 'suburban-relocation' ); ?></p><h2><?php esc_html_e( 'Let’s plan a smoother move.', 'suburban-relocation' ); ?></h2></div><a class="srs-button" href="#quote"><?php esc_html_e( 'Get a free quote', 'suburban-relocation' ); ?> →</a></div>
</section>
