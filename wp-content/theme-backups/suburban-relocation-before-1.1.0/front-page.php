<?php
/** Editable Gutenberg front page. */
get_header();
?>
<main id="main-content" class="srs-front-page">
	<?php
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	?>
</main>
<?php get_footer(); ?>
