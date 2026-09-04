<?php
/** Site footer. */
$services = get_posts(
	array(
		'post_type'      => 'srs_service',
		'posts_per_page' => 5,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'order'          => 'ASC',
	)
);
?>
<footer class="srs-site-footer">
	<div class="srs-container srs-footer-grid">
		<div class="srs-footer-brand">
			<?php srs_the_logo(); ?>
			<p><?php esc_html_e( 'Thoughtful planning, careful handling and clear communication for every mile of your move.', 'suburban-relocation' ); ?></p>
		</div>
		<div>
			<h2><?php esc_html_e( 'Services', 'suburban-relocation' ); ?></h2>
			<?php foreach ( $services as $service ) : ?><a href="<?php echo esc_url( get_permalink( $service ) ); ?>"><?php echo esc_html( get_the_title( $service ) ); ?></a><?php endforeach; ?>
		</div>
		<div>
			<h2><?php esc_html_e( 'Company', 'suburban-relocation' ); ?></h2>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'srs_location' ) ); ?>"><?php esc_html_e( 'Locations', 'suburban-relocation' ); ?></a>
			<?php foreach ( array( 'testimonials', 'moving-tips', 'contact' ) as $slug ) : $page = get_page_by_path( $slug ); if ( $page ) : ?><a href="<?php echo esc_url( get_permalink( $page ) ); ?>"><?php echo esc_html( get_the_title( $page ) ); ?></a><?php endif; endforeach; ?>
		</div>
		<div>
			<h2><?php esc_html_e( 'Talk to us', 'suburban-relocation' ); ?></h2>
			<a href="tel:<?php echo esc_attr( srs_phone_href() ); ?>"><?php echo esc_html( srs_get_option( 'phone', '800-816-6834' ) ); ?></a>
			<a href="mailto:<?php echo esc_attr( srs_get_option( 'email', 'info@srelocation.com' ) ); ?>"><?php echo esc_html( srs_get_option( 'email', 'info@srelocation.com' ) ); ?></a>
			<span><?php echo esc_html( srs_get_option( 'hours', 'Mon–Sun, 8am–8pm' ) ); ?></span>
			<span><?php echo esc_html( srs_get_option( 'address', '12000 Old Baltimore Pike, Beltsville, MD 20705' ) ); ?></span>
		</div>
	</div>
	<div class="srs-container srs-footer-bottom">
		<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
		<span>USDOT <?php echo esc_html( srs_get_option( 'usdot', '2562098' ) ); ?> · MC <?php echo esc_html( srs_get_option( 'mc', '894071' ) ); ?> · TX DOT <?php echo esc_html( srs_get_option( 'txdot', '007049119C' ) ); ?></span>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
