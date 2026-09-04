<?php
/** Locations archive. */
get_header();
?>
<main id="main-content">
	<section class="srs-page-hero">
		<div class="srs-container"><p class="srs-eyebrow"><?php esc_html_e( 'Local teams · Nationwide reach', 'suburban-relocation' ); ?></p><h1><?php post_type_archive_title(); ?></h1><p><?php esc_html_e( 'Find detailed moving information for your nearest service area.', 'suburban-relocation' ); ?></p></div>
	</section>
	<section class="srs-archive-section">
		<div class="srs-container">
			<div class="srs-location-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<a class="srs-location-card" href="<?php the_permalink(); ?>"><span class="srs-location-pin" aria-hidden="true">●</span><div><p class="srs-card-label"><?php echo esc_html( srs_meta( '_srs_state', get_the_ID(), __( 'Service area', 'suburban-relocation' ) ) ); ?></p><h2><?php the_title(); ?></h2><p><?php echo esc_html( srs_meta( '_srs_address', get_the_ID(), get_the_excerpt() ) ); ?></p><strong><?php esc_html_e( 'View location', 'suburban-relocation' ); ?> →</strong></div></a>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>
