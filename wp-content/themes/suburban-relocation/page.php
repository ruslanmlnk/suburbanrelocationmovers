<?php
/** Standard editable page. */
get_header();
?>
<main id="main-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<section class="srs-page-hero"><div class="srs-container"><p class="srs-eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p><h1><?php the_title(); ?></h1><?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?></div></section>
		<section class="srs-page-content"><article class="srs-container srs-prose"><?php the_content(); ?><?php wp_link_pages(); ?></article></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
