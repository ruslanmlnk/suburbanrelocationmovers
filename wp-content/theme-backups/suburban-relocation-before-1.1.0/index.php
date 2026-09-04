<?php
/** Blog index and fallback. */
get_header();
?>
<main id="main-content">
	<section class="srs-page-hero"><div class="srs-container"><p class="srs-eyebrow"><?php esc_html_e( 'Helpful resources', 'suburban-relocation' ); ?></p><h1><?php echo esc_html( is_home() ? __( 'Moving Tips', 'suburban-relocation' ) : get_the_archive_title() ); ?></h1></div></section>
	<section class="srs-archive-section"><div class="srs-container"><div class="srs-post-grid">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?><article class="srs-post-card"><?php if ( has_post_thumbnail() ) : ?><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a><?php endif; ?><p class="srs-card-label"><?php echo esc_html( get_the_date() ); ?></p><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html( get_the_excerpt() ); ?></p><a class="srs-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read article', 'suburban-relocation' ); ?> →</a></article><?php endwhile; else : ?><p><?php esc_html_e( 'No posts found.', 'suburban-relocation' ); ?></p><?php endif; ?>
	</div><?php the_posts_pagination(); ?></div></section>
</main>
<?php get_footer(); ?>
