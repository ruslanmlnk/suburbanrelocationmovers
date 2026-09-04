<?php
/** Stable section-by-section homepage editor. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function srs_home_defaults() {
	return array(
		'hero_kicker'       => 'Moving made refreshingly clear',
		'hero_title'        => 'A smoother move starts with a better plan.',
		'hero_text'         => 'Personal move coordination, careful crews and dependable service for homes and businesses—nearby or across the country.',
		'hero_button'       => 'Plan my move',
		'hero_image'        => get_theme_file_uri( 'assets/images/hero-moving.png' ),
		'form_kicker'       => 'Free estimate',
		'form_title'        => 'Tell us about your move',
		'form_text'         => 'Get a clear, no-obligation estimate from a coordinator.',
		'form_button'       => 'Get my free estimate',
		'trust_enabled'     => '1',
		'trust_1_title'     => 'Careful handling',
		'trust_1_text'      => 'From door to door',
		'trust_2_title'     => 'Professional crews',
		'trust_2_text'      => 'Prepared for every move',
		'trust_3_title'     => 'Dedicated support',
		'trust_3_text'      => 'One team, clear updates',
		'services_enabled'  => '1',
		'services_kicker'   => 'What we do',
		'services_title'    => 'Moving services built around your day.',
		'services_text'     => 'From the first box to the final walkthrough, our team keeps every detail organized, protected and moving forward.',
		'process_enabled'   => '1',
		'process_kicker'    => 'Simple by design',
		'process_title'     => 'Your entire move, handled in three clear steps.',
		'process_text'      => 'No guessing and no chasing updates. You’ll know what happens next, who to contact and how your moving day is progressing.',
		'step_1_title'      => 'Share the details',
		'step_1_text'       => 'Tell us where, when and what you’re moving.',
		'step_2_title'      => 'Approve your plan',
		'step_2_text'       => 'Review the scope, schedule and clear estimate.',
		'step_3_title'      => 'Move with confidence',
		'step_3_text'       => 'Your crew handles the heavy work and keeps you updated.',
		'locations_enabled' => '1',
		'locations_kicker'  => 'Service area',
		'locations_title'   => 'Local expertise. Long-distance reach.',
		'locations_text'    => 'Our coordinators plan residential and commercial relocations throughout the Washington, DC region and long-distance moves across the United States.',
		'reviews_enabled'   => '1',
		'reviews_kicker'    => 'Real moving stories',
		'reviews_title'     => 'Care you can feel from the first call.',
		'reviews_rating'    => '5.0',
		'review_1_quote'    => 'The crew arrived on time, protected every doorway and kept us informed throughout the move. The entire day felt organized from start to finish.',
		'review_1_name'     => 'Rachel M.',
		'review_1_detail'   => 'Residential move · Maryland',
		'review_2_quote'    => 'Our office relocation had a tight schedule. The team planned each stage, labeled everything clearly and had us back to work quickly.',
		'review_2_name'     => 'Daniel K.',
		'review_2_detail'   => 'Commercial move · Washington, DC',
		'review_3_quote'    => 'From the first call to final delivery, communication was excellent. Nothing was rushed and every piece arrived exactly as packed.',
		'review_3_name'     => 'Nicole T.',
		'review_3_detail'   => 'Long-distance move · Virginia',
		'faq_enabled'       => '1',
		'faq_kicker'        => 'Good to know',
		'faq_title'         => 'Questions before moving day?',
		'faq_text'          => 'A little clarity goes a long way. Here are the answers people usually need first.',
		'faq_1_question'    => 'How early should I book my move?',
		'faq_1_answer'      => 'Two to four weeks ahead is ideal. Summer, month-end and weekend dates fill fastest, so earlier is always better when your date is fixed.',
		'faq_2_question'    => 'Can your team help with packing?',
		'faq_2_answer'      => 'Yes. Choose full packing, help with selected rooms, or moving supplies only. We can tailor the scope during your estimate.',
		'faq_3_question'    => 'What information is needed for an estimate?',
		'faq_3_answer'      => 'Your origin, destination, move date, home size and any special items are enough to begin. A coordinator can confirm the remaining details with you.',
		'cta_enabled'       => '1',
		'cta_kicker'        => 'Ready when you are',
		'cta_title'         => 'Let’s make your next move feel lighter.',
		'cta_button'        => 'Get a free quote',
	);
}

function srs_get_home_option( $key ) {
	$options  = get_option( 'srs_home_sections', array() );
	$defaults = srs_home_defaults();
	return isset( $options[ $key ] ) ? $options[ $key ] : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

function srs_home_settings_menu() {
	add_theme_page(
		__( 'Home Sections', 'suburban-relocation' ),
		__( 'Home Sections', 'suburban-relocation' ),
		'edit_theme_options',
		'srs-home-sections',
		'srs_render_home_settings'
	);
}
add_action( 'admin_menu', 'srs_home_settings_menu' );

function srs_register_home_settings() {
	register_setting( 'srs_home_settings', 'srs_home_sections', 'srs_sanitize_home_settings' );
}
add_action( 'admin_init', 'srs_register_home_settings' );

function srs_sanitize_home_settings( $input ) {
	$defaults = srs_home_defaults();
	$output   = array();
	foreach ( $defaults as $key => $default ) {
		if ( false !== strpos( $key, '_enabled' ) ) {
			$output[ $key ] = isset( $input[ $key ] ) ? '1' : '0';
		} elseif ( 'hero_image' === $key ) {
			$output[ $key ] = isset( $input[ $key ] ) ? esc_url_raw( $input[ $key ] ) : $default;
		} elseif ( false !== strpos( $key, '_text' ) || false !== strpos( $key, '_quote' ) || false !== strpos( $key, '_answer' ) ) {
			$output[ $key ] = isset( $input[ $key ] ) ? sanitize_textarea_field( $input[ $key ] ) : $default;
		} else {
			$output[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : $default;
		}
	}
	return $output;
}

function srs_home_field( $key, $label, $type = 'text' ) {
	$value = srs_get_home_option( $key );
	?>
	<label class="srs-admin-field">
		<span><?php echo esc_html( $label ); ?></span>
		<?php if ( 'textarea' === $type ) : ?>
			<textarea name="srs_home_sections[<?php echo esc_attr( $key ); ?>]" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
		<?php else : ?>
			<input type="<?php echo esc_attr( $type ); ?>" name="srs_home_sections[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>">
		<?php endif; ?>
	</label>
	<?php
}

function srs_home_toggle( $key ) {
	?>
	<label class="srs-admin-toggle"><input type="checkbox" name="srs_home_sections[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( srs_get_home_option( $key ), '1' ); ?>> <?php esc_html_e( 'Show this section', 'suburban-relocation' ); ?></label>
	<?php
}

function srs_render_home_settings() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	?>
	<div class="wrap srs-home-admin">
		<h1><?php esc_html_e( 'Home Page Sections', 'suburban-relocation' ); ?></h1>
		<p><?php esc_html_e( 'Edit the visible homepage content here. The theme keeps the layout fixed and professional while you change every text and image.', 'suburban-relocation' ); ?></p>
		<form action="options.php" method="post">
			<?php settings_fields( 'srs_home_settings' ); ?>
			<div class="srs-admin-sections">
				<section><h2>Hero</h2><?php srs_home_field( 'hero_kicker', 'Kicker' ); srs_home_field( 'hero_title', 'Main heading' ); srs_home_field( 'hero_text', 'Description', 'textarea' ); srs_home_field( 'hero_button', 'Button text' ); ?><label class="srs-admin-field"><span>Background image</span><span class="srs-media-row"><input id="srs_hero_image" type="url" name="srs_home_sections[hero_image]" value="<?php echo esc_attr( srs_get_home_option( 'hero_image' ) ); ?>"><button class="button srs-upload-image" type="button" data-target="#srs_hero_image">Choose image</button></span></label></section>
				<section><h2>Quote form</h2><?php srs_home_field( 'form_kicker', 'Kicker' ); srs_home_field( 'form_title', 'Heading' ); srs_home_field( 'form_text', 'Description', 'textarea' ); srs_home_field( 'form_button', 'Submit button' ); ?></section>
				<section><h2>Trust strip</h2><?php srs_home_toggle( 'trust_enabled' ); for ( $i = 1; $i <= 3; $i++ ) { echo '<h3>Item ' . esc_html( $i ) . '</h3>'; srs_home_field( 'trust_' . $i . '_title', 'Title' ); srs_home_field( 'trust_' . $i . '_text', 'Text' ); } ?></section>
				<section><h2>Services</h2><?php srs_home_toggle( 'services_enabled' ); srs_home_field( 'services_kicker', 'Kicker' ); srs_home_field( 'services_title', 'Heading' ); srs_home_field( 'services_text', 'Description', 'textarea' ); ?><p class="description">Service cards are managed under <strong>Services</strong>.</p></section>
				<section><h2>Process</h2><?php srs_home_toggle( 'process_enabled' ); srs_home_field( 'process_kicker', 'Kicker' ); srs_home_field( 'process_title', 'Heading' ); srs_home_field( 'process_text', 'Description', 'textarea' ); for ( $i = 1; $i <= 3; $i++ ) { echo '<h3>Step ' . esc_html( $i ) . '</h3>'; srs_home_field( 'step_' . $i . '_title', 'Title' ); srs_home_field( 'step_' . $i . '_text', 'Text', 'textarea' ); } ?></section>
				<section><h2>Locations</h2><?php srs_home_toggle( 'locations_enabled' ); srs_home_field( 'locations_kicker', 'Kicker' ); srs_home_field( 'locations_title', 'Heading' ); srs_home_field( 'locations_text', 'Description', 'textarea' ); ?><p class="description">Cards are managed under <strong>Locations</strong>. Use the “Show on home” checkbox in a location.</p></section>
				<section><h2>Reviews</h2><?php srs_home_toggle( 'reviews_enabled' ); srs_home_field( 'reviews_kicker', 'Kicker' ); srs_home_field( 'reviews_title', 'Heading' ); srs_home_field( 'reviews_rating', 'Rating' ); for ( $i = 1; $i <= 3; $i++ ) { echo '<h3>Review ' . esc_html( $i ) . '</h3>'; srs_home_field( 'review_' . $i . '_quote', 'Quote', 'textarea' ); srs_home_field( 'review_' . $i . '_name', 'Customer' ); srs_home_field( 'review_' . $i . '_detail', 'Move details' ); } ?></section>
				<section><h2>FAQ</h2><?php srs_home_toggle( 'faq_enabled' ); srs_home_field( 'faq_kicker', 'Kicker' ); srs_home_field( 'faq_title', 'Heading' ); srs_home_field( 'faq_text', 'Description', 'textarea' ); for ( $i = 1; $i <= 3; $i++ ) { echo '<h3>Question ' . esc_html( $i ) . '</h3>'; srs_home_field( 'faq_' . $i . '_question', 'Question' ); srs_home_field( 'faq_' . $i . '_answer', 'Answer', 'textarea' ); } ?></section>
				<section><h2>Final CTA</h2><?php srs_home_toggle( 'cta_enabled' ); srs_home_field( 'cta_kicker', 'Kicker' ); srs_home_field( 'cta_title', 'Heading' ); srs_home_field( 'cta_button', 'Button text' ); ?></section>
			</div>
			<?php submit_button( __( 'Save home page', 'suburban-relocation' ) ); ?>
		</form>
	</div>
	<style>
	.srs-home-admin{max-width:1180px}.srs-admin-sections{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-top:24px}.srs-admin-sections section{padding:22px;border:1px solid #dcdcde;border-radius:12px;background:#fff}.srs-admin-sections h2{margin-top:0;padding-bottom:12px;border-bottom:1px solid #eee}.srs-admin-sections h3{margin:22px 0 8px;color:#1f5f9d}.srs-admin-field{display:block;margin:12px 0}.srs-admin-field>span:first-child{display:block;margin-bottom:5px;font-weight:600}.srs-admin-field input,.srs-admin-field textarea{width:100%}.srs-admin-toggle{display:inline-block;margin:0 0 12px;padding:8px 10px;border-radius:6px;background:#eef4fa;font-weight:600}.srs-media-row{display:flex;gap:8px}.srs-media-row input{flex:1}@media(max-width:850px){.srs-admin-sections{grid-template-columns:1fr}}
	</style>
	<script>
	jQuery(function($){$('.srs-upload-image').on('click',function(){var target=$($(this).data('target'));var frame=wp.media({title:'Choose hero image',button:{text:'Use image'},multiple:false});frame.on('select',function(){target.val(frame.state().get('selection').first().toJSON().url)});frame.open()})});
	</script>
	<?php
}

function srs_home_admin_assets( $hook ) {
	if ( 'appearance_page_srs-home-sections' === $hook ) {
		wp_enqueue_media();
		wp_enqueue_script( 'jquery' );
	}
}
add_action( 'admin_enqueue_scripts', 'srs_home_admin_assets' );
