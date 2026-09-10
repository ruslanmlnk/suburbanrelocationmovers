<?php
/**
 * Suburban Relocation Systems theme bootstrap.
 *
 * @package Suburban_Relocation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SRS_THEME_VERSION', '1.2.1' );

/** Replace the former OSPanel origin in imported editable content on production. */
function srs_replace_local_origin( $value ) {
	if ( ! is_string( $value ) ) return $value;
	return str_replace(
		array( 'http://suburbanrelocationmovers', 'https://suburbanrelocationmovers' ),
		rtrim( home_url(), '/' ),
		$value
	);
}
add_filter( 'the_content', 'srs_replace_local_origin', 5 );
add_filter( 'widget_text_content', 'srs_replace_local_origin', 5 );

function srs_replace_local_nav_origin( $atts ) {
	if ( isset( $atts['href'] ) ) $atts['href'] = srs_replace_local_origin( $atts['href'] );
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'srs_replace_local_nav_origin' );

require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/meta-boxes.php';
require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/home-settings.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/form-handler.php';
require_once get_template_directory() . '/inc/form-integrations.php';
require_once get_template_directory() . '/inc/form-integrations-admin.php';
require_once get_template_directory() . '/inc/starter-content.php';
require_once get_template_directory() . '/inc/legacy-content.php';
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
	wp_enqueue_script( 'srs-quote-forms', get_theme_file_uri( 'assets/js/quote-forms.js' ), array(), filemtime( get_theme_file_path( 'assets/js/quote-forms.js' ) ), true );
}
add_action( 'wp_enqueue_scripts', 'srs_enqueue_assets' );

/** Add useful body classes. */
function srs_body_classes( $classes ) {
	if ( is_singular( array( 'srs_service', 'srs_location', 'srs_city' ) ) ) {
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
	$digits = preg_replace( '/[^0-9]/', '', $phone );
	if ( 10 === strlen( $digits ) ) {
		$digits = '1' . $digits;
	}
	return '+' . $digits;
}

/** Inline SVG icons matching the reference artwork. */
function srs_icon( $name ) {
	$paths = array(
		'arrow-right' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'arrow-left' => '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
		'arrow-up-right' => '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>',
		'phone' => '<path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/>',
		'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6h4"/>',
		'package-check' => '<path d="M12 22V12"/><path d="m16 17 2 2 4-4"/><path d="M21 11.127V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.729l7 4a2 2 0 0 0 2 .001l1.32-.753"/><path d="M3.29 7 12 12l8.71-5"/>',
		'shield-check' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
		'headphones' => '<path d="M4 14a8 8 0 0 1 16 0"/><path d="M18 19c0 1.7-1.3 3-3 3h-1"/><path d="M4 14v4a2 2 0 0 0 2 2h1v-8H6a2 2 0 0 0-2 2Z"/><path d="M20 14v4a2 2 0 0 1-2 2h-1v-8h1a2 2 0 0 1 2 2Z"/>',
		'check' => '<path d="M20 6 9 17l-5-5"/>',
		'map-pin' => '<path d="M20 10c0 5-5.5 11-8 12-2.5-1-8-7-8-12a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
		'truck' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
		'home' => '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
		'building' => '<path d="M10 12h4"/><path d="M10 8h4"/><path d="M14 21v-3a2 2 0 0 0-4 0v3"/><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/>',
		'warehouse' => '<path d="M18 21V10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v11"/><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 1.132-1.803l7.95-3.974a2 2 0 0 1 1.837 0l7.948 3.974A2 2 0 0 1 22 8z"/><path d="M6 13h12"/><path d="M6 17h12"/>',
		'sparkles' => '<path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) return '';
	return '<svg class="srs-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[ $name ] . '</svg>';
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

	if ( in_array( get_post_type( $post_id ), array( 'srs_location', 'srs_city' ), true ) ) {
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

/** Every historic public HTML URL, including aliases and original misspellings. */
function srs_legacy_url_map() {
	return array(
		'residential.html' => array( 'srs_service', 'residential-moving' ),
		'local.html' => array( 'srs_service', 'local-moving' ),
		'long.html' => array( 'srs_service', 'long-distance-moving' ),
		'commercial.html' => array( 'srs_service', 'commercial-moving' ),
		'international.html' => array( 'srs_service', 'international-moving' ),
		'intenational.html' => array( 'srs_service', 'international-moving' ),
		'maryland.html' => array( 'srs_location', 'maryland' ),
		'washington-dc-movers.html' => array( 'srs_location', 'washington-dc' ),
		'virginia.html' => array( 'srs_location', 'virginia' ),
		'colorado.html' => array( 'srs_location', 'colorado' ),
		'california.html' => array( 'srs_location', 'california' ),
		'texas.html' => array( 'srs_location', 'texas' ),
		'annapolis-md-movers.html' => array( 'srs_location', 'annapolis' ),
		'applewood-movers.html' => array( 'srs_location', 'applewood' ),
		'arlington-movers.html' => array( 'srs_location', 'arlington' ),
		'ashburn-movers.html' => array( 'srs_location', 'ashburn' ),
		'aspen-park-movers.html' => array( 'srs_location', 'aspen-park' ),
		'aurora-movers.html' => array( 'srs_location', 'aurora' ),
		'baltimore-md-movers.html' => array( 'srs_location', 'baltimore' ),
		'beverly-hills-movers.html' => array( 'srs_location', 'beverly-hills' ),
		'boulder-movers.html' => array( 'srs_location', 'boulder' ),
		'brighton-movers.html' => array( 'srs_location', 'brighton' ),
		'broomfield-movers.html' => array( 'srs_location', 'broomfield' ),
		'castle-rock-movers.html' => array( 'srs_location', 'castle-rock' ),
		'centennial-movers.html' => array( 'srs_location', 'centennial' ),
		'centreville-movers.html' => array( 'srs_location', 'centreville' ),
		'chester-movers.html' => array( 'srs_location', 'chester' ),
		'chevy-chase-movers.html' => array( 'srs_location', 'chevy-chase' ),
		'college-park-movers.html' => array( 'srs_location', 'college-park' ),
		'colorado-springs-movers.html' => array( 'srs_location', 'colorado-springs' ),
		'columbia-md-movers.html' => array( 'srs_location', 'columbia-md' ),
		'commerce-city-movers.html' => array( 'srs_location', 'commerce-city' ),
		'conifer-movers.html' => array( 'srs_location', 'conifer' ),
		'denver-co-movers.html' => array( 'srs_location', 'denver' ),
		'derwood-movers.html' => array( 'srs_location', 'derwood' ),
		'edgewater-movers.html' => array( 'srs_location', 'edgewater' ),
		'englewood-movers.html' => array( 'srs_location', 'englewood' ),
		'evergreen-movers.html' => array( 'srs_location', 'evergreen' ),
		'fairfax-movers.html' => array( 'srs_location', 'fairfax' ),
		'fort-collings-movers.html' => array( 'srs_location', 'fort-collins' ),
		'gaithersburg-movers.html' => array( 'srs_location', 'gaithersburg' ),
		'garrett-park-movers.html' => array( 'srs_location', 'garrett-park' ),
		'golden-movers.html' => array( 'srs_location', 'golden' ),
		'greeley-movers.html' => array( 'srs_location', 'greeley' ),
		'green-village-movers.html' => array( 'srs_location', 'green-village' ),
		'greenbelt-movers.html' => array( 'srs_location', 'greenbelt' ),
		'highland-ranch-movers.html' => array( 'srs_location', 'highlands-ranch' ),
		'hollywood-movers.html' => array( 'srs_location', 'hollywood' ),
		'hyattsville-movers.html' => array( 'srs_location', 'hyattsville' ),
		'irvine-movers.html' => array( 'srs_location', 'irvine' ),
		'kensington-movers.html' => array( 'srs_location', 'kensington' ),
		'lafayette-movers.html' => array( 'srs_location', 'lafayette' ),
		'lakewood-movers.html' => array( 'srs_location', 'lakewood' ),
		'laural-va-movers.html' => array( 'srs_location', 'laurel-va' ),
		'laurel-md-movers.html' => array( 'srs_location', 'laurel-md' ),
		'littleton-movers.html' => array( 'srs_location', 'littleton' ),
		'longmont-movers.html' => array( 'srs_location', 'longmont' ),
		'lorton-movers.html' => array( 'srs_location', 'lorton' ),
		'los-angeles-ca-movers.html' => array( 'srs_location', 'los-angeles' ),
		'louisville-movers.html' => array( 'srs_location', 'louisville' ),
		'loveland-movers.html' => array( 'srs_location', 'loveland' ),
		'manassas-movers.html' => array( 'srs_location', 'manassas' ),
		'mc-lean-movers.html' => array( 'srs_location', 'mclean' ),
		'montgomery-movers.html' => array( 'srs_location', 'montgomery' ),
		'monument-movers.html' => array( 'srs_location', 'monument' ),
		'newport-news-movers.html' => array( 'srs_location', 'newport-news' ),
		'norfolk-movers.html' => array( 'srs_location', 'norfolk' ),
		'northglenn-henderson-movers.html' => array( 'srs_location', 'northglenn-henderson' ),
		'olney-movers.html' => array( 'srs_location', 'olney' ),
		'parker-movers.html' => array( 'srs_location', 'parker' ),
		'potomac-movers.html' => array( 'srs_location', 'potomac' ),
		'rockville-movers.html' => array( 'srs_location', 'rockville' ),
		'sandy-spring-movers.html' => array( 'srs_location', 'sandy-spring' ),
		'silver-spring-movers.html' => array( 'srs_location', 'silver-spring' ),
		'springfield-movers.html' => array( 'srs_location', 'springfield' ),
		'sterling-movers.html' => array( 'srs_location', 'sterling' ),
		'thornton-movers.html' => array( 'srs_location', 'thornton' ),
		'towson-movers.html' => array( 'srs_location', 'towson' ),
		'upper-marlboro-movers.html' => array( 'srs_location', 'upper-marlboro' ),
		'vienna-movers.html' => array( 'srs_location', 'vienna' ),
		'wellington-movers.html' => array( 'srs_location', 'wellington' ),
		'westminister-movers.html' => array( 'srs_location', 'westminster' ),
		'westminster-movers.html' => array( 'srs_location', 'westminster' ),
		'woodbridge-movers.html' => array( 'srs_location', 'woodbridge' ),
		'woodland-park-movers.html' => array( 'srs_location', 'woodland-park' ),
		'woodmoor-movers.html' => array( 'srs_location', 'woodmoor' ),
		'moving-alexandria.html' => array( 'srs_location', 'alexandria' ),
		'moving-annandale.html' => array( 'srs_location', 'alexandria' ),
		'moving-arlington.html' => array( 'srs_location', 'arlington' ),
		'moving-ashburn.html' => array( 'srs_location', 'ashburn' ),
		'moving-bethesda.html' => array( 'srs_location', 'bethesda' ),
		'moving-centreville.html' => array( 'srs_location', 'centreville' ),
		'moving-chester.html' => array( 'srs_location', 'chester' ),
		'moving-chevy-chase.html' => array( 'srs_location', 'chevy-chase' ),
		'moving-college-park.html' => array( 'srs_location', 'college-park' ),
		'moving-columbia.html' => array( 'srs_location', 'columbia-md' ),
		'moving-dc.html' => array( 'srs_location', 'washington-dc' ),
		'moving-derwood.html' => array( 'srs_location', 'derwood' ),
		'moving-fairfax.html' => array( 'srs_location', 'fairfax' ),
		'moving-gaithersburg.html' => array( 'srs_location', 'gaithersburg' ),
		'moving-garrett-park.html' => array( 'srs_location', 'garrett-park' ),
		'moving-greenbelt.html' => array( 'srs_location', 'greenbelt' ),
		'moving-hyattsville.html' => array( 'srs_location', 'hyattsville' ),
		'moving-kensington.html' => array( 'srs_location', 'kensington' ),
		'moving-laurel.html' => array( 'srs_location', 'laurel-md' ),
		'moving-lney.html' => array( 'srs_location', 'olney' ),
		'moving-lorton.html' => array( 'srs_location', 'lorton' ),
		'moving-manassas.html' => array( 'srs_location', 'manassas' ),
		'moving-mc-lean.html' => array( 'srs_location', 'mclean' ),
		'moving-montgomery-village.html' => array( 'srs_location', 'montgomery' ),
		'moving-newport-news.html' => array( 'srs_location', 'newport-news' ),
		'moving-norfolk.html' => array( 'srs_location', 'norfolk' ),
		'moving-potomac.html' => array( 'srs_location', 'potomac' ),
		'moving-rockville.html' => array( 'srs_location', 'rockville' ),
		'moving-sandy-spring.html' => array( 'srs_location', 'sandy-spring' ),
		'moving-silver-spring.html' => array( 'srs_location', 'silver-spring' ),
		'moving-springfield.html' => array( 'srs_location', 'springfield' ),
		'moving-sterling.html' => array( 'srs_location', 'sterling' ),
		'moving-towson.html' => array( 'srs_location', 'towson' ),
		'moving-upper-marlboro.html' => array( 'srs_location', 'upper-marlboro' ),
		'moving-vienna.html' => array( 'srs_location', 'vienna' ),
		'moving-woodbridge.html' => array( 'srs_location', 'woodbridge' ),
	);
}

/** Preserve the exact public URLs used by the approved site and the historic SEO structure. */
function srs_reference_routes() {
	$state_slugs = array( 'maryland', 'washington-dc', 'virginia', 'colorado', 'california', 'texas' );
	foreach ( srs_legacy_url_map() as $url => $target ) {
		$post_type = ( 'srs_location' === $target[0] && ! in_array( $target[1], $state_slugs, true ) ) ? 'srs_city' : $target[0];
		add_rewrite_rule( '^' . preg_quote( $url, '/' ) . '$', 'index.php?post_type=' . $post_type . '&name=' . $target[1], 'top' );
	}
	/* Keep the generic city fallback after exact historic routes. Otherwise
	 * washington-dc-movers.html is mistaken for a city instead of the DC state page. */
	add_rewrite_rule( '^([^/]+)-movers\.html$', 'index.php?post_type=srs_city&name=$matches[1]', 'top' );
	foreach ( array( 'testimonials', 'moving-tips', 'contact' ) as $slug ) {
		$file = 'moving-tips' === $slug ? 'tip.html' : ( 'contact' === $slug ? 'contact-us.html' : 'testimonials.html' );
		add_rewrite_rule( '^' . preg_quote( $file, '/' ) . '$', 'index.php?pagename=' . $slug, 'top' );
	}
	add_rewrite_rule( '^index\.html$', 'index.php', 'top' );
	add_rewrite_rule( '^services\.html$', 'index.php?post_type=srs_service', 'top' );
	add_rewrite_rule( '^services-area\.html$', 'index.php?post_type=srs_location', 'top' );
	add_rewrite_rule( '^our(?:-|_)location\.html$', 'index.php?post_type=srs_location', 'top' );
	add_rewrite_rule( '^sitemap\.html$', 'index.php?pagename=sitemap', 'top' );
	add_rewrite_rule( '^track-your-shipment\.html$', 'index.php?pagename=track-your-shipment', 'top' );
}
add_action( 'init', 'srs_reference_routes', 20 );

function srs_reference_permalink( $url, $post ) {
	$map = array();
	foreach ( srs_legacy_url_map() as $file => $target ) {
		if ( ! isset( $map[ $target[1] ] ) ) $map[ $target[1] ] = $file;
	}
	if ( isset( $map[ $post->post_name ] ) ) return home_url( '/' . $map[ $post->post_name ] );
	if ( 'srs_city' === $post->post_type ) return home_url( '/' . $post->post_name . '-movers.html' );
	return $url;
}
add_filter( 'post_type_link', 'srs_reference_permalink', 10, 2 );

function srs_reference_archive_permalink( $url, $post_type ) {
	if ( 'srs_service' === $post_type ) return home_url( '/services.html' );
	if ( 'srs_location' === $post_type ) return home_url( '/our-location.html' );
	return $url;
}
add_filter( 'post_type_archive_link', 'srs_reference_archive_permalink', 10, 2 );

function srs_reference_page_permalink( $url, $post_id ) {
	$post = get_post( $post_id );
	$map = array(
		'testimonials'        => 'testimonials.html',
		'moving-tips'         => 'tip.html',
		'contact'             => 'contact-us.html',
		'sitemap'             => 'sitemap.html',
		'track-your-shipment' => 'track-your-shipment.html',
	);
	return $post && isset( $map[ $post->post_name ] ) ? home_url( '/' . $map[ $post->post_name ] ) : $url;
}
add_filter( 'page_link', 'srs_reference_page_permalink', 10, 2 );
add_filter( 'redirect_canonical', function( $redirect, $requested ) { return preg_match( '/\.html(?:\?.*)?$/i', $requested ) ? false : $redirect; }, 10, 2 );

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
