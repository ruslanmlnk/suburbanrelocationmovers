<?php
/** Services archive. */
get_header();
?>
<main id="main-content">
	<section class="srs-page-hero">
		<div class="srs-container"><p class="srs-eyebrow"><?php esc_html_e( 'Professional relocation support', 'suburban-relocation' ); ?></p><h1><?php post_type_archive_title(); ?></h1><p><?php esc_html_e( 'Explore residential, local, long-distance, commercial and international moving services.', 'suburban-relocation' ); ?></p></div>
	</section>
	<section class="srs-archive-section">
		<div class="srs-container">
			<div class="srs-archive-grid srs-service-archive-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="srs-archive-card">
						<a class="srs-archive-image" href="<?php the_permalink(); ?>"><img src="<?php echo esc_url( srs_featured_image_url() ); ?>" alt="<?php the_title_attribute(); ?>"></a>
						<div><p class="srs-card-label"><?php echo esc_html( srs_meta( '_srs_card_label', get_the_ID(), __( 'Moving service', 'suburban-relocation' ) ) ); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html( get_the_excerpt() ); ?></p><a class="srs-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read service article', 'suburban-relocation' ); ?> →</a></div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		</div>
	</section>
	<section class="srs-inline-quote"><div class="srs-container"><?php echo do_shortcode( '[srs_quote_form title="Plan your move" text="Share the basics and receive a no-obligation estimate."]' ); ?></div></section>
</main>
<?php get_footer(); ?>
