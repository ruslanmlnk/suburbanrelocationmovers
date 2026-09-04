<?php
/**
 * Suburban Relocation Systems theme bootstrap.
 *
 * @package Suburban_Relocation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SRS_THEME_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/meta-boxes.php';
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/form-handler.php';
require_once get_template_directory() . '/inc/starter-content.php';
require_once get_template_directory() . '/inc/patterns.php';

/** Configure theme features. */
function srs_theme_setup() {
	load_theme_textdomain( 'suburban-relocation', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 92,
			'width'       => 504,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'suburban-relocation' ),
			'footer'  => __( 'Footer navigation', 'suburban-relocation' ),
		)
	);
}
add_action( 'after_setup_theme', 'srs_theme_setup' );

/** Load front-end assets. */
function srs_enqueue_assets() {
	wp_enqueue_style( 'srs-theme', get_template_directory_uri() . '/assets/css/theme.css', array(), SRS_THEME_VERSION );
	wp_enqueue_script( 'srs-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), SRS_THEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'srs_enqueue_assets' );

/** Add useful body classes. */
function srs_body_classes( $classes ) {
	if ( is_singular( array( 'srs_service', 'srs_location' ) ) ) {
		$classes[] = 'srs-article-page';
	}
	return $classes;
}
add_filter( 'body_class', 'srs_body_classes' );

/** Read a theme setting with a safe default. */
function srs_get_option( $key, $default = '' ) {
	$options = get_option( 'srs_theme_options', array() );
	return isset( $options[ $key ] ) && '' !== $options[ $key ] ? $options[ $key ] : $default;
}

/** Convert a display phone number into a tel: value. */
function srs_phone_href( $phone = '' ) {
	$phone = $phone ? $phone : srs_get_option( 'phone', '800-816-6834' );
	return '+' . ltrim( preg_replace( '/[^0-9]/', '', $phone ), '+' );
}

/** Logo with an archived-brand fallback. */
function srs_the_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	?>
	<a class="srs-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/logo.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	</a>
	<?php
}

/** Image fallback for starter posts before a featured image is selected. */
function srs_featured_image_url( $post_id = 0, $size = 'large' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$url     = get_the_post_thumbnail_url( $post_id, $size );
	if ( $url ) {
		return $url;
	}

	if ( 'srs_location' === get_post_type( $post_id ) ) {
		return get_theme_file_uri( 'assets/images/hero-moving.png' );
	}

	$slug = get_post_field( 'post_name', $post_id );
	if ( false !== strpos( $slug, 'commercial' ) ) {
		return get_theme_file_uri( 'assets/images/commercial-moving.webp' );
	}
	if ( false !== strpos( $slug, 'long' ) || false !== strpos( $slug, 'international' ) ) {
		return get_theme_file_uri( 'assets/images/long-distance-moving.webp' );
	}
	return get_theme_file_uri( 'assets/images/residential-moving.webp' );
}

/** Read a post meta value with a fallback. */
function srs_meta( $key, $post_id = 0, $default = '' ) {
	$value = get_post_meta( $post_id ? $post_id : get_the_ID(), $key, true );
	return '' !== $value ? $value : $default;
}

/** Default navigation shown until a custom menu is assigned. */
function srs_fallback_menu() {
	$services = get_posts(
		array(
			'post_type'      => 'srs_service',
			'posts_per_page' => 8,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'order'          => 'ASC',
		)
	);
	$locations = get_posts(
		array(
			'post_type'      => 'srs_location',
			'posts_per_page' => 8,
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'order'          => 'ASC',
		)
	);
	?>
	<ul class="srs-menu">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'suburban-relocation' ); ?></a></li>
		<li class="menu-item-has-children"><a href="<?php echo esc_url( get_post_type_archive_link( 'srs_service' ) ); ?>"><?php esc_html_e( 'Services', 'suburban-relocation' ); ?></a>
			<ul class="sub-menu">
				<?php foreach ( $services as $service ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $service ) ); ?>"><?php echo esc_html( get_the_title( $service ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</li>
		<li class="menu-item-has-children"><a href="<?php echo esc_url( get_post_type_archive_link( 'srs_location' ) ); ?>"><?php esc_html_e( 'Our Locations', 'suburban-relocation' ); ?></a>
			<ul class="sub-menu">
				<?php foreach ( $locations as $location ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $location ) ); ?>"><?php echo esc_html( get_the_title( $location ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</li>
		<?php foreach ( array( 'testimonials', 'moving-tips', 'contact' ) as $page_slug ) : ?>
			<?php $page = get_page_by_path( $page_slug ); ?>
			<?php if ( $page ) : ?><li><a href="<?php echo esc_url( get_permalink( $page ) ); ?>"><?php echo esc_html( get_the_title( $page ) ); ?></a></li><?php endif; ?>
		<?php endforeach; ?>
	</ul>
	<?php
}

/** Flush rewrites only when the active theme version changes. */
function srs_maybe_flush_rewrites() {
	if ( SRS_THEME_VERSION !== get_option( 'srs_theme_rewrite_version' ) ) {
		flush_rewrite_rules();
		update_option( 'srs_theme_rewrite_version', SRS_THEME_VERSION );
	}
}
add_action( 'init', 'srs_maybe_flush_rewrites', 99 );

/** Use practical page sizes for the custom archives. */
function srs_archive_page_sizes( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'srs_location' ) ) {
		$query->set( 'posts_per_page', 24 );
	}
	if ( $query->is_post_type_archive( 'srs_service' ) ) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'srs_archive_page_sizes' );
