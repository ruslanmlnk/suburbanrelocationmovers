<?php
/** Dynamic content grids and quote form. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function srs_register_shortcodes() {
	add_shortcode( 'srs_services_grid', 'srs_services_grid_shortcode' );
	add_shortcode( 'srs_locations_grid', 'srs_locations_grid_shortcode' );
	add_shortcode( 'srs_quote_form', 'srs_quote_form_shortcode' );
}
add_action( 'init', 'srs_register_shortcodes' );

function srs_services_grid_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'count' => '4' ), $atts, 'srs_services_grid' );
	$count = max( -1, min( 24, intval( $atts['count'] ) ) );
	$query = new WP_Query(
		array(
			'post_type'      => 'srs_service',
			'post_status'    => 'publish',
			'posts_per_page' => $count,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			'order'          => 'ASC',
		)
	);
	if ( ! $query->have_posts() ) {
		return current_user_can( 'edit_posts' ) ? '<p class="srs-editor-hint">' . esc_html__( 'Add Services in the WordPress dashboard to populate this section.', 'suburban-relocation' ) . '</p>' : '';
	}

	ob_start();
	?>
	<div class="srs-service-grid">
		<?php $number = 1; ?>
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<article class="srs-service-card">
				<div class="srs-card-top">
					<span class="srs-card-icon" aria-hidden="true">↗</span>
					<span class="srs-card-number"><?php echo esc_html( str_pad( (string) $number, 2, '0', STR_PAD_LEFT ) ); ?></span>
				</div>
				<?php if ( srs_meta( '_srs_card_label' ) ) : ?><p class="srs-card-label"><?php echo esc_html( srs_meta( '_srs_card_label' ) ); ?></p><?php endif; ?>
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
				<a class="srs-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Explore service', 'suburban-relocation' ); ?> <span aria-hidden="true">→</span></a>
			</article>
			<?php ++$number; ?>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}

function srs_locations_grid_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'count' => '-1', 'featured' => 'yes' ), $atts, 'srs_locations_grid' );
	$count = max( -1, min( 50, intval( $atts['count'] ) ) );
	$args = array(
			'post_type'      => 'srs_location',
			'post_status'    => 'publish',
			'posts_per_page' => $count,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'order'          => 'ASC',
	);
	if ( 'yes' === strtolower( $atts['featured'] ) ) {
		$args['meta_key']   = '_srs_featured';
		$args['meta_value'] = '1';
	}
	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return current_user_can( 'edit_posts' ) ? '<p class="srs-editor-hint">' . esc_html__( 'Add Locations in the WordPress dashboard to populate this section.', 'suburban-relocation' ) . '</p>' : '';
	}

	ob_start();
	?>
	<div class="srs-location-grid">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<a class="srs-location-card" href="<?php the_permalink(); ?>">
				<span class="srs-location-pin" aria-hidden="true">●</span>
				<div>
					<p class="srs-card-label"><?php echo esc_html( srs_meta( '_srs_state', get_the_ID(), __( 'Service area', 'suburban-relocation' ) ) ); ?></p>
					<h3><?php the_title(); ?></h3>
					<p><?php echo esc_html( srs_meta( '_srs_address', get_the_ID(), get_the_excerpt() ) ); ?></p>
					<strong><?php esc_html_e( 'View location', 'suburban-relocation' ); ?> <span aria-hidden="true">→</span></strong>
				</div>
			</a>
		<?php endwhile; ?>
	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}

function srs_quote_form_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'kicker' => __( 'Free estimate', 'suburban-relocation' ),
			'title'  => __( 'Tell us about your move', 'suburban-relocation' ),
			'text'   => __( 'Get a clear, no-obligation estimate from a coordinator.', 'suburban-relocation' ),
			'button' => __( 'Get my free estimate', 'suburban-relocation' ),
			'compact' => 'no',
		),
		$atts,
		'srs_quote_form'
	);
	$success = isset( $_GET['srs_quote'] ) && 'success' === sanitize_key( wp_unslash( $_GET['srs_quote'] ) );
	$error   = isset( $_GET['srs_quote'] ) && 'missing-phone' === sanitize_key( wp_unslash( $_GET['srs_quote'] ) );
	$compact = 'yes' === strtolower( $atts['compact'] );

	ob_start();
	?>
	<div class="srs-quote-card<?php echo $compact ? ' is-compact' : ''; ?>" id="quote">
		<?php if ( $success ) : ?>
			<div class="srs-form-success" role="status">
				<span aria-hidden="true">✓</span>
				<p class="srs-form-kicker"><?php esc_html_e( 'Request received', 'suburban-relocation' ); ?></p>
				<h2><?php esc_html_e( 'Your move is on our radar.', 'suburban-relocation' ); ?></h2>
				<p><?php esc_html_e( 'A relocation coordinator will contact you shortly to confirm the details.', 'suburban-relocation' ); ?></p>
			</div>
		<?php else : ?>
			<div class="srs-form-heading">
				<p class="srs-form-kicker"><?php echo esc_html( $atts['kicker'] ); ?></p>
				<h2><?php echo esc_html( $atts['title'] ); ?></h2>
				<p><?php echo esc_html( $atts['text'] ); ?></p>
			</div>
			<?php if ( $error ) : ?><p class="srs-form-error" role="alert"><?php esc_html_e( 'Please enter a phone number.', 'suburban-relocation' ); ?></p><?php endif; ?>
			<form class="srs-quote-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="srs_submit_quote">
				<input type="hidden" name="source_url" value="<?php echo esc_url( srs_current_url() ); ?>">
				<?php wp_nonce_field( 'srs_quote_request', 'srs_quote_nonce' ); ?>
				<label class="srs-honeypot" aria-hidden="true">Website<input name="website" tabindex="-1" autocomplete="off"></label>
				<div class="srs-form-grid">
					<label><span><?php esc_html_e( 'Full name', 'suburban-relocation' ); ?> <small><?php esc_html_e( '(optional)', 'suburban-relocation' ); ?></small></span><input name="full_name" autocomplete="name" placeholder="<?php esc_attr_e( 'Full name', 'suburban-relocation' ); ?>"></label>
					<label><span><?php esc_html_e( 'Phone number', 'suburban-relocation' ); ?> <small class="required"><?php esc_html_e( '(required)', 'suburban-relocation' ); ?></small></span><input type="tel" name="phone" autocomplete="tel" placeholder="<?php esc_attr_e( 'Phone number', 'suburban-relocation' ); ?>" required></label>
					<label><span><?php esc_html_e( 'Preferred move date', 'suburban-relocation' ); ?> <small><?php esc_html_e( '(optional)', 'suburban-relocation' ); ?></small></span><input type="date" name="move_date" autocomplete="off"></label>
					<label><span><?php esc_html_e( 'Email', 'suburban-relocation' ); ?> <small><?php esc_html_e( '(optional)', 'suburban-relocation' ); ?></small></span><input type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Email address', 'suburban-relocation' ); ?>"></label>
					<label><span><?php esc_html_e( 'Moving from', 'suburban-relocation' ); ?> <small><?php esc_html_e( '(optional)', 'suburban-relocation' ); ?></small></span><input name="moving_from" autocomplete="postal-code" placeholder="<?php esc_attr_e( 'Origin ZIP', 'suburban-relocation' ); ?>"></label>
					<label><span><?php esc_html_e( 'Moving to', 'suburban-relocation' ); ?> <small><?php esc_html_e( '(optional)', 'suburban-relocation' ); ?></small></span><input name="moving_to" autocomplete="postal-code" placeholder="<?php esc_attr_e( 'Destination ZIP', 'suburban-relocation' ); ?>"></label>
				</div>
				<button class="srs-button srs-form-submit" type="submit"><?php echo esc_html( $atts['button'] ); ?> <span aria-hidden="true">→</span></button>
				<p class="srs-form-note"><span aria-hidden="true">●</span> <?php esc_html_e( 'Your information stays private and secure.', 'suburban-relocation' ); ?></p>
			</form>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

function srs_current_url() {
	$scheme = is_ssl() ? 'https://' : 'http://';
	$host   = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : wp_parse_url( home_url(), PHP_URL_HOST );
	$uri    = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	return esc_url_raw( $scheme . $host . $uri );
}
