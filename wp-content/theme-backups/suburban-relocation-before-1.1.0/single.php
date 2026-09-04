<?php
/** Blog post. */
get_header();
?>
<main id="main-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<section class="srs-page-hero"><div class="srs-container"><p class="srs-eyebrow"><?php echo esc_html( get_the_date() ); ?></p><h1><?php the_title(); ?></h1></div></section>
		<section class="srs-page-content"><article class="srs-container srs-prose"><?php the_content(); ?><?php wp_link_pages(); ?></article></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
