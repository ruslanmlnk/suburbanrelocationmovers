<?php
get_header();
?>
<main id="main-content"><section class="srs-empty-state"><div class="srs-container"><p class="srs-eyebrow dark">404</p><h1><?php esc_html_e( 'This page has moved.', 'suburban-relocation' ); ?></h1><p><?php esc_html_e( 'Let’s get you back to the main site.', 'suburban-relocation' ); ?></p><a class="srs-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'suburban-relocation' ); ?></a></div></section></main>
<?php get_footer(); ?>
