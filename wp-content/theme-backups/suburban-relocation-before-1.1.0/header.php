<?php
/** Site header. */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="srs-skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'suburban-relocation' ); ?></a>
<header class="srs-site-header">
	<div class="srs-utility-bar">
		<div class="srs-container srs-utility-inner">
			<p><?php esc_html_e( 'Professional moving support, seven days a week', 'suburban-relocation' ); ?></p>
			<div><span><?php echo esc_html( srs_get_option( 'hours', 'Mon–Sun, 8am–8pm' ) ); ?></span><a href="tel:<?php echo esc_attr( srs_phone_href() ); ?>"><?php echo esc_html( srs_get_option( 'phone', '800-816-6834' ) ); ?></a></div>
		</div>
	</div>
	<div class="srs-container srs-header-inner">
		<div class="srs-brand"><?php srs_the_logo(); ?></div>
		<button class="srs-menu-toggle" type="button" aria-expanded="false" aria-controls="srs-primary-nav"><span></span><span></span><span></span><span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'suburban-relocation' ); ?></span></button>
		<nav class="srs-primary-nav" id="srs-primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'suburban-relocation' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'srs-menu',
						'depth'          => 2,
					)
				);
			} else {
				srs_fallback_menu();
			}
			?>
		</nav>
		<a class="srs-header-cta" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Free quote', 'suburban-relocation' ); ?> <span aria-hidden="true">→</span></a>
	</div>
</header>
