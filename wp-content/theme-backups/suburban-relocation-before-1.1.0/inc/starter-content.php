<?php
/** One-click starter content importer. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/starter-data.php';

function srs_starter_content_menu() {
	add_theme_page(
		__( 'Starter Content', 'suburban-relocation' ),
		__( 'Starter Content', 'suburban-relocation' ),
		'edit_theme_options',
		'srs-starter-content',
		'srs_render_starter_content_page'
	);
}
add_action( 'admin_menu', 'srs_starter_content_menu' );

function srs_starter_admin_notice() {
	if ( get_option( 'srs_starter_imported' ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$url = admin_url( 'themes.php?page=srs-starter-content' );
	?>
	<div class="notice notice-info is-dismissible">
		<p><strong><?php esc_html_e( 'Suburban Relocation theme is ready.', 'suburban-relocation' ); ?></strong> <?php esc_html_e( 'Import the editable home sections, services, locations and menus to match the demo.', 'suburban-relocation' ); ?> <a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Open Starter Content', 'suburban-relocation' ); ?></a></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'srs_starter_admin_notice' );

function srs_render_starter_content_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$done  = get_option( 'srs_starter_imported' );
	$count = isset( $_GET['srs_imported'] ) ? absint( $_GET['srs_imported'] ) : 0;
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Suburban Relocation Starter Content', 'suburban-relocation' ); ?></h1>
		<?php if ( $count ) : ?><div class="notice notice-success inline"><p><?php echo esc_html( sprintf( __( '%d new items were created. Existing content was left unchanged.', 'suburban-relocation' ), $count ) ); ?></p></div><?php endif; ?>
		<p><?php esc_html_e( 'This creates an editable Gutenberg home page, five detailed service articles, six primary locations, all archived city service-area pages, supporting pages and navigation menus.', 'suburban-relocation' ); ?></p>
		<ul style="list-style:disc;padding-left:22px">
			<li><?php esc_html_e( 'Each home section remains editable as its own Group, Cover, Columns, Heading, Paragraph, Button, Details or Shortcode block.', 'suburban-relocation' ); ?></li>
			<li><?php esc_html_e( 'New Services and Locations automatically appear in shortcode grids and archive pages.', 'suburban-relocation' ); ?></li>
			<li><?php esc_html_e( 'The importer never overwrites a post or page that already uses the same slug.', 'suburban-relocation' ); ?></li>
		</ul>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="srs_import_starter_content">
			<?php wp_nonce_field( 'srs_import_starter_content', 'srs_import_nonce' ); ?>
			<?php submit_button( $done ? __( 'Import any missing starter content', 'suburban-relocation' ) : __( 'Import starter content', 'suburban-relocation' ), 'primary', 'submit', false ); ?>
		</form>
		<?php if ( $done ) : ?><p class="description"><?php echo esc_html( sprintf( __( 'Last completed: %s', 'suburban-relocation' ), $done ) ); ?></p><?php endif; ?>
	</div>
	<?php
}

function srs_import_starter_content() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to import this content.', 'suburban-relocation' ) );
	}
	check_admin_referer( 'srs_import_starter_content', 'srs_import_nonce' );

	$count       = 0;
	$service_ids = array();
	$location_ids = array();
	$image_ids   = array();
	foreach ( array( 'hero-moving.png', 'residential-moving.webp', 'long-distance-moving.webp', 'commercial-moving.webp', 'logo.png' ) as $filename ) {
		$image_ids[ $filename ] = srs_import_theme_image( $filename );
	}

	foreach ( srs_starter_services() as $index => $service ) {
		$existing = get_page_by_path( $service['slug'], OBJECT, 'srs_service' );
		if ( $existing ) {
			$post_id = $existing->ID;
		} else {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'srs_service',
					'post_status'  => 'publish',
					'post_title'   => $service['title'],
					'post_name'    => $service['slug'],
					'post_excerpt' => $service['excerpt'],
					'post_content' => srs_build_article_content( $service['sections'], get_theme_file_uri( 'assets/images/' . $service['image'] ), $service['title'] ),
					'menu_order'   => $index,
				)
			);
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				++$count;
			}
		}
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$service_ids[] = $post_id;
			srs_set_meta_if_empty( $post_id, '_srs_kicker', $service['kicker'] );
			srs_set_meta_if_empty( $post_id, '_srs_card_label', $service['label'] );
			if ( ! has_post_thumbnail( $post_id ) && ! empty( $image_ids[ $service['image'] ] ) ) {
				set_post_thumbnail( $post_id, $image_ids[ $service['image'] ] );
			}
		}
	}

	foreach ( srs_starter_primary_locations() as $index => $location ) {
		$existing = get_page_by_path( $location['slug'], OBJECT, 'srs_location' );
		if ( $existing ) {
			$post_id = $existing->ID;
		} else {
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'srs_location',
					'post_status'  => 'publish',
					'post_title'   => $location['title'],
					'post_name'    => $location['slug'],
					'post_excerpt' => $location['excerpt'],
					'post_content' => srs_build_location_content( $location ),
					'menu_order'   => $index,
				)
			);
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				++$count;
			}
		}
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$location_ids[] = $post_id;
			srs_set_location_meta( $post_id, $location );
			srs_set_meta_if_empty( $post_id, '_srs_featured', '1' );
			if ( ! has_post_thumbnail( $post_id ) && ! empty( $image_ids['hero-moving.png'] ) ) {
				set_post_thumbnail( $post_id, $image_ids['hero-moving.png'] );
			}
		}
	}

	foreach ( srs_starter_city_locations() as $index => $city ) {
		$existing = get_page_by_path( $city[0], OBJECT, 'srs_location' );
		$city_name = str_replace( ' Movers', '', $city[1] );
		if ( $existing ) {
			$post_id = $existing->ID;
		} else {
			$city_location = array(
				'title'   => $city[1],
				'slug'    => $city[0],
				'state'   => 'Local service area',
				'kicker'  => 'Local moving service',
				'address' => 'Serving ' . $city_name . ' and surrounding communities',
				'area'    => 'Local and long-distance routes from ' . $city_name,
				'excerpt' => 'Professional local and long-distance moving support for homes and businesses in ' . $city_name . '.',
			);
			$post_id = wp_insert_post(
				array(
					'post_type'    => 'srs_location',
					'post_status'  => 'publish',
					'post_title'   => $city[1],
					'post_name'    => $city[0],
					'post_excerpt' => $city_location['excerpt'],
					'post_content' => srs_build_location_content( $city_location ),
					'menu_order'   => 100 + $index,
				)
			);
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				++$count;
			}
		}
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$meta_location = isset( $city_location ) ? $city_location : array(
				'state' => 'Local service area', 'kicker' => 'Local moving service',
				'address' => 'Serving ' . $city_name . ' and surrounding communities', 'area' => 'Local and long-distance routes from ' . $city_name,
			);
			srs_set_location_meta( $post_id, $meta_location );
			srs_set_meta_if_empty( $post_id, '_srs_featured', '0' );
		}
		unset( $city_location );
	}

	$home_id = srs_import_page( 'Home', 'home', srs_home_page_content(), $count );
	$testimonials_id = srs_import_page( 'Testimonials', 'testimonials', srs_testimonials_page_content(), $count );
	$tips_id = srs_import_page( 'Moving Tips', 'moving-tips', srs_tips_page_content(), $count );
	$contact_id = srs_import_page( 'Contact', 'contact', srs_contact_page_content(), $count );

	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	if ( false === get_option( 'srs_theme_options', false ) ) {
		update_option( 'srs_theme_options', srs_option_defaults() );
	}

	srs_create_starter_menus( $home_id, $service_ids, $location_ids, array_filter( array( $testimonials_id, $tips_id, $contact_id ) ) );
	update_option( 'srs_starter_imported', current_time( 'mysql' ) );
	flush_rewrite_rules();

	wp_safe_redirect( add_query_arg( array( 'page' => 'srs-starter-content', 'srs_imported' => $count ), admin_url( 'themes.php' ) ) );
	exit;
}
add_action( 'admin_post_srs_import_starter_content', 'srs_import_starter_content' );

function srs_import_page( $title, $slug, $content, &$count ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing ) {
		return $existing->ID;
	}
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		)
	);
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		++$count;
		return $post_id;
	}
	return 0;
}

function srs_set_meta_if_empty( $post_id, $key, $value ) {
	if ( '' === get_post_meta( $post_id, $key, true ) ) {
		update_post_meta( $post_id, $key, $value );
	}
}

function srs_set_location_meta( $post_id, $location ) {
	srs_set_meta_if_empty( $post_id, '_srs_kicker', isset( $location['kicker'] ) ? $location['kicker'] : 'Local moving service' );
	srs_set_meta_if_empty( $post_id, '_srs_state', isset( $location['state'] ) ? $location['state'] : 'Service area' );
	srs_set_meta_if_empty( $post_id, '_srs_address', isset( $location['address'] ) ? $location['address'] : '' );
	srs_set_meta_if_empty( $post_id, '_srs_local_phone', '800-816-6834' );
	srs_set_meta_if_empty( $post_id, '_srs_service_area', isset( $location['area'] ) ? $location['area'] : '' );
}

function srs_import_theme_image( $filename ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_srs_theme_asset',
			'meta_value'     => $filename,
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$source = get_theme_file_path( 'assets/images/' . $filename );
	if ( ! file_exists( $source ) || ! is_readable( $source ) ) {
		return 0;
	}
	$upload = wp_upload_bits( $filename, null, file_get_contents( $source ) );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$filetype = wp_check_filetype( $upload['file'] );
	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_text_field( pathinfo( $filename, PATHINFO_FILENAME ) ),
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( ! $attach_id || is_wp_error( $attach_id ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
	wp_update_attachment_metadata( $attach_id, $metadata );
	update_post_meta( $attach_id, '_srs_theme_asset', $filename );
	return $attach_id;
}

function srs_build_article_content( $sections, $image_url = '', $image_alt = '' ) {
	$content = '';
	foreach ( $sections as $index => $section ) {
		$content .= '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html( $section['title'] ) . '</h2><!-- /wp:heading -->';
		foreach ( $section['body'] as $paragraph ) {
			$content .= '<!-- wp:paragraph --><p>' . esc_html( $paragraph ) . '</p><!-- /wp:paragraph -->';
		}
		if ( 1 === $index && $image_url ) {
			$content .= '<!-- wp:image {"sizeSlug":"large","className":"srs-article-image"} --><figure class="wp-block-image size-large srs-article-image"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image_alt ) . '"/></figure><!-- /wp:image -->';
		}
		if ( ! empty( $section['bullets'] ) ) {
			$content .= '<!-- wp:list --><ul class="wp-block-list">';
			foreach ( $section['bullets'] as $bullet ) {
				$content .= '<!-- wp:list-item --><li>' . esc_html( $bullet ) . '</li><!-- /wp:list-item -->';
			}
			$content .= '</ul><!-- /wp:list -->';
		}
	}
	return $content;
}

function srs_build_location_content( $location ) {
	$place = str_replace( ' Movers', '', $location['title'] );
	$sections = array(
		array( 'title' => 'A moving plan built for ' . $place, 'body' => array( 'Every move begins with the route, inventory, access and schedule. A coordinator uses those details to plan the crew, truck, equipment and services for your home or business.', 'Local knowledge and clear communication help the day run smoothly from the first walkthrough to final placement.' ) ),
		array( 'title' => 'Residential and local moving', 'body' => array( 'Our crews protect furniture, floors and doorways, then load the truck for stability and an efficient delivery. Apartment, condominium and house moves can include full packing, partial packing or moving-only support.', 'Share elevator times, parking rules and any delicate or oversized items before moving day so the team can arrive prepared.' ), 'bullets' => array( 'Homes and apartments', 'Packing and unpacking options', 'Furniture protection', 'Local moving routes', 'Short-term storage' ) ),
		array( 'title' => 'Long-distance and commercial relocation', 'body' => array( 'Long-distance moves use a documented inventory, transport-ready packing and coordinated pickup and delivery milestones. Commercial moves add department labels, building requirements and schedules designed to reduce downtime.', 'The same coordinator can connect packing, transportation, storage and final placement in one practical plan.' ) ),
		array( 'title' => 'Request an estimate', 'body' => array( 'To begin, share your phone number and any details already available: preferred date, origin and destination ZIP codes, property type and special items. A coordinator can collect the remaining information during the follow-up.', 'Service area: ' . $location['area'] . '. ' . $location['address'] . '.' ) ),
	);
	return srs_build_article_content( $sections, get_theme_file_uri( 'assets/images/residential-moving.webp' ), $location['title'] );
}

function srs_home_page_content() {
	$hero  = esc_url( get_theme_file_uri( 'assets/images/hero-moving.png' ) );
	$phone = esc_html( srs_get_option( 'phone', '800-816-6834' ) );
	$tel   = esc_url( 'tel:' . srs_phone_href( $phone ) );
	$content = <<<'HTML'
<!-- wp:cover {"url":"{{HERO}}","dimRatio":70,"overlayColor":"navy","minHeight":760,"minHeightUnit":"px","anchor":"top","className":"srs-home-hero","layout":{"type":"constrained"}} -->
<div class="wp-block-cover srs-home-hero" id="top" style="min-height:760px"><span aria-hidden="true" class="wp-block-cover__background has-navy-background-color has-background-dim-70 has-background-dim"></span><img class="wp-block-cover__image-background" alt="Professional movers" src="{{HERO}}" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:columns {"verticalAlignment":"center","className":"srs-container srs-hero-columns"} -->
<div class="wp-block-columns are-vertically-aligned-center srs-container srs-hero-columns"><!-- wp:column {"verticalAlignment":"center","width":"57%","className":"srs-hero-copy"} -->
<div class="wp-block-column is-vertically-aligned-center srs-hero-copy" style="flex-basis:57%"><!-- wp:paragraph {"className":"srs-eyebrow"} --><p class="srs-eyebrow">Moving made refreshingly clear</p><!-- /wp:paragraph --><!-- wp:heading {"level":1,"fontSize":"x-large"} --><h1 class="wp-block-heading has-x-large-font-size">A smoother move starts with a better plan.</h1><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"medium"} --><p class="has-medium-font-size">Personal move coordination, careful crews and dependable service for homes and businesses—nearby or across the country.</p><!-- /wp:paragraph --><!-- wp:buttons {"className":"srs-hero-actions"} --><div class="wp-block-buttons srs-hero-actions"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#quote">Plan my move →</a></div><!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="{{TEL}}">Call {{PHONE}}</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"43%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:43%"><!-- wp:shortcode -->[srs_quote_form]<!-- /wp:shortcode --></div><!-- /wp:column --></div><!-- /wp:columns --></div></div><!-- /wp:cover -->
<!-- wp:group {"className":"srs-trust-wrap","layout":{"type":"constrained"}} --><div class="wp-block-group srs-trust-wrap"><!-- wp:columns {"className":"srs-container srs-trust-strip"} --><div class="wp-block-columns srs-container srs-trust-strip"><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Careful handling</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Protected from door to door</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Professional crews</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Prepared for every move</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Dedicated support</h3><!-- /wp:heading --><!-- wp:paragraph --><p>One team, clear updates</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->
<!-- wp:group {"tagName":"section","anchor":"services","className":"srs-section srs-services-section","layout":{"type":"constrained"}} --><section class="wp-block-group srs-section srs-services-section" id="services"><!-- wp:group {"className":"srs-container","layout":{"type":"constrained"}} --><div class="wp-block-group srs-container"><!-- wp:columns {"verticalAlignment":"bottom","className":"srs-section-head"} --><div class="wp-block-columns are-vertically-aligned-bottom srs-section-head"><!-- wp:column {"verticalAlignment":"bottom","width":"60%"} --><div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:60%"><!-- wp:paragraph {"className":"srs-eyebrow dark"} --><p class="srs-eyebrow dark">What we do</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Moving services built around your day.</h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"bottom","width":"40%"} --><div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:40%"><!-- wp:paragraph --><p>From the first box to the final walkthrough, our team keeps every detail organized, protected and moving forward.</p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:shortcode -->[srs_services_grid count="-1"]<!-- /wp:shortcode --></div><!-- /wp:group --></section><!-- /wp:group -->
<!-- wp:group {"tagName":"section","anchor":"process","className":"srs-section srs-process-section","layout":{"type":"constrained"}} --><section class="wp-block-group srs-section srs-process-section" id="process"><!-- wp:columns {"verticalAlignment":"center","className":"srs-container srs-process-grid"} --><div class="wp-block-columns are-vertically-aligned-center srs-container srs-process-grid"><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"srs-eyebrow"} --><p class="srs-eyebrow">Simple by design</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Your entire move, handled in three clear steps.</h2><!-- /wp:heading --><!-- wp:paragraph --><p>No guessing and no chasing updates. You’ll know what happens next, who to contact and how your moving day is progressing.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:group {"className":"srs-step","layout":{"type":"flex","flexWrap":"nowrap"}} --><div class="wp-block-group srs-step"><!-- wp:paragraph --><p>01</p><!-- /wp:paragraph --><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Share the details</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Tell us where, when and what you’re moving.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group --><!-- wp:group {"className":"srs-step","layout":{"type":"flex","flexWrap":"nowrap"}} --><div class="wp-block-group srs-step"><!-- wp:paragraph --><p>02</p><!-- /wp:paragraph --><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Approve your plan</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Review the scope, schedule and clear estimate.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group --><!-- wp:group {"className":"srs-step","layout":{"type":"flex","flexWrap":"nowrap"}} --><div class="wp-block-group srs-step"><!-- wp:paragraph --><p>03</p><!-- /wp:paragraph --><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Move with confidence</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Your crew handles the heavy work and keeps you updated.</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group --></div><!-- /wp:column --></div><!-- /wp:columns --></section><!-- /wp:group -->
<!-- wp:group {"tagName":"section","anchor":"locations","className":"srs-section srs-locations-section","layout":{"type":"constrained"}} --><section class="wp-block-group srs-section srs-locations-section" id="locations"><!-- wp:group {"className":"srs-container","layout":{"type":"constrained"}} --><div class="wp-block-group srs-container"><!-- wp:paragraph {"className":"srs-eyebrow dark"} --><p class="srs-eyebrow dark">Service areas</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Local expertise. Long-distance reach.</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Explore our primary service locations. Add or edit a Location in the dashboard and this grid updates automatically.</p><!-- /wp:paragraph --><!-- wp:shortcode -->[srs_locations_grid count="-1" featured="yes"]<!-- /wp:shortcode --></div><!-- /wp:group --></section><!-- /wp:group -->
<!-- wp:group {"tagName":"section","anchor":"reviews","className":"srs-section srs-reviews-section","layout":{"type":"constrained"}} --><section class="wp-block-group srs-section srs-reviews-section" id="reviews"><!-- wp:columns {"verticalAlignment":"center","className":"srs-container"} --><div class="wp-block-columns are-vertically-aligned-center srs-container"><!-- wp:column {"verticalAlignment":"center","width":"35%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:35%"><!-- wp:paragraph {"className":"srs-eyebrow"} --><p class="srs-eyebrow">Real moving stories</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Care you can feel from the first call.</h2><!-- /wp:heading --><!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">★★★★★ 5.0</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"65%","className":"srs-review-card"} --><div class="wp-block-column is-vertically-aligned-center srs-review-card" style="flex-basis:65%"><!-- wp:quote --><blockquote class="wp-block-quote"><p>The crew arrived on time, protected every doorway and kept us informed throughout the move. The entire day felt organized from start to finish.</p><cite>Rachel M. · Residential move</cite></blockquote><!-- /wp:quote --></div><!-- /wp:column --></div><!-- /wp:columns --></section><!-- /wp:group -->
<!-- wp:group {"tagName":"section","className":"srs-section srs-faq-section","layout":{"type":"constrained"}} --><section class="wp-block-group srs-section srs-faq-section"><!-- wp:columns {"className":"srs-container"} --><div class="wp-block-columns srs-container"><!-- wp:column {"width":"40%"} --><div class="wp-block-column" style="flex-basis:40%"><!-- wp:paragraph {"className":"srs-eyebrow dark"} --><p class="srs-eyebrow dark">Good to know</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Questions before moving day?</h2><!-- /wp:heading --><!-- wp:paragraph --><p>A little clarity goes a long way. Edit, remove or add FAQ blocks here whenever needed.</p><!-- /wp:paragraph --></div><!-- /wp:column --><!-- wp:column {"width":"60%"} --><div class="wp-block-column" style="flex-basis:60%"><!-- wp:details --><details class="wp-block-details"><summary>How early should I book my move?</summary><!-- wp:paragraph --><p>Two to four weeks ahead is ideal. Summer, month-end and weekend dates fill fastest, so earlier is better when your date is fixed.</p><!-- /wp:paragraph --></details><!-- /wp:details --><!-- wp:details --><details class="wp-block-details"><summary>Can your team help with packing?</summary><!-- wp:paragraph --><p>Yes. Choose full packing, help with selected rooms, or moving supplies only. We can tailor the scope during your estimate.</p><!-- /wp:paragraph --></details><!-- /wp:details --><!-- wp:details --><details class="wp-block-details"><summary>What information is needed for an estimate?</summary><!-- wp:paragraph --><p>Your origin, destination, move date, home size and any special items are enough to begin. A coordinator can confirm the remaining details with you.</p><!-- /wp:paragraph --></details><!-- /wp:details --></div><!-- /wp:column --></div><!-- /wp:columns --></section><!-- /wp:group -->
<!-- wp:group {"tagName":"section","className":"srs-final-cta","layout":{"type":"constrained"}} --><section class="wp-block-group srs-final-cta"><!-- wp:columns {"verticalAlignment":"center","className":"srs-container"} --><div class="wp-block-columns are-vertically-aligned-center srs-container"><!-- wp:column {"verticalAlignment":"center","width":"70%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:70%"><!-- wp:paragraph {"className":"srs-eyebrow"} --><p class="srs-eyebrow">Ready when you are</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Let’s make your next move feel lighter.</h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","width":"30%"} --><div class="wp-block-column is-vertically-aligned-center" style="flex-basis:30%"><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"right"}} --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#quote">Get a free quote →</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:column --></div><!-- /wp:columns --></section><!-- /wp:group -->
HTML;
	return strtr( $content, array( '{{HERO}}' => $hero, '{{PHONE}}' => $phone, '{{TEL}}' => $tel ) );
}

function srs_testimonials_page_content() {
	return '<!-- wp:heading --><h2 class="wp-block-heading">What customers say about moving with us</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Customer experiences can be edited, reordered or replaced with any Gutenberg blocks.</p><!-- /wp:paragraph --><!-- wp:columns {"className":"srs-testimonial-grid"} --><div class="wp-block-columns srs-testimonial-grid"><!-- wp:column --><div class="wp-block-column"><!-- wp:quote --><blockquote class="wp-block-quote"><p>The crew handled our delicate belongings and older furniture with real care. The move was organized from beginning to end.</p><cite>Verified moving customer</cite></blockquote><!-- /wp:quote --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:quote --><blockquote class="wp-block-quote"><p>Setting up the move was simple, every detail was explained clearly, and the team stayed friendly and prompt.</p><cite>Verified moving customer</cite></blockquote><!-- /wp:quote --></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:columns {"className":"srs-testimonial-grid"} --><div class="wp-block-columns srs-testimonial-grid"><!-- wp:column --><div class="wp-block-column"><!-- wp:quote --><blockquote class="wp-block-quote"><p>Our move went smoothly. The crew arrived prepared and everything reached the new home safely.</p><cite>Verified moving customer</cite></blockquote><!-- /wp:quote --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:quote --><blockquote class="wp-block-quote"><p>The team turned a stressful relocation into a much easier day. Communication and service exceeded our expectations.</p><cite>Verified moving customer</cite></blockquote><!-- /wp:quote --></div><!-- /wp:column --></div><!-- /wp:columns -->';
}

function srs_tips_page_content() {
	$tips = array(
		'Build a moving plan' => 'List what needs to move, your move-out deadline, access details and whether storage or professional packing will be needed.',
		'Move less' => 'Sell, donate or recycle items you no longer need before packing. A smaller inventory saves time, space and cost.',
		'Use quality supplies' => 'Choose sturdy boxes, strong tape, protective paper, blankets and wardrobe cartons suited to what you are packing.',
		'Create an inventory' => 'Number each box and note its contents. This makes delivery checks and unpacking much faster.',
		'Color-code by room' => 'Assign a color to each room and label boxes clearly so movers can place them in the right area immediately.',
		'Contact your mover early' => 'Early planning gives you more choice of dates and enough time to organize packing, access and storage.',
	);
	$content = '<!-- wp:heading --><h2 class="wp-block-heading">Six practical ways to prepare</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Take a breath, start early and use a simple system to keep the move manageable.</p><!-- /wp:paragraph -->';
	foreach ( $tips as $title => $text ) {
		$content .= '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html( $title ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html( $text ) . '</p><!-- /wp:paragraph -->';
	}
	return $content;
}

function srs_contact_page_content() {
	return '<!-- wp:heading --><h2 class="wp-block-heading">We look forward to learning about your move</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Call <a href="tel:' . esc_attr( srs_phone_href() ) . '">' . esc_html( srs_get_option( 'phone', '800-816-6834' ) ) . '</a> or send your details using the form below.</p><!-- /wp:paragraph --><!-- wp:shortcode -->[srs_quote_form compact="no" title="Start your moving estimate" text="Share the basics and a moving coordinator will follow up."]<!-- /wp:shortcode -->';
}

function srs_create_starter_menus( $home_id, $service_ids, $location_ids, $page_ids ) {
	$menu_obj = wp_get_nav_menu_object( 'Primary Menu' );
	$menu_id  = $menu_obj ? $menu_obj->term_id : wp_create_nav_menu( 'Primary Menu' );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}
	$items = wp_get_nav_menu_items( $menu_id );
	if ( empty( $items ) ) {
		if ( $home_id ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Home', 'menu-item-object' => 'page', 'menu-item-object-id' => $home_id, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		}
		$services_parent = wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Services', 'menu-item-url' => get_post_type_archive_link( 'srs_service' ), 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
		foreach ( $service_ids as $post_id ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => get_the_title( $post_id ), 'menu-item-object' => 'srs_service', 'menu-item-object-id' => $post_id, 'menu-item-parent-id' => $services_parent, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		}
		$locations_parent = wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'Our Locations', 'menu-item-url' => get_post_type_archive_link( 'srs_location' ), 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
		foreach ( array_slice( $location_ids, 0, 6 ) as $post_id ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => get_the_title( $post_id ), 'menu-item-object' => 'srs_location', 'menu-item-object-id' => $post_id, 'menu-item-parent-id' => $locations_parent, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		}
		foreach ( $page_ids as $post_id ) {
			wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => get_the_title( $post_id ), 'menu-item-object' => 'page', 'menu-item-object-id' => $post_id, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
		}
	}
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) ) {
		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
