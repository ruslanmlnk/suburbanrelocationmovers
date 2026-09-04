<?php
/** Global company settings. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function srs_settings_menu() {
	add_theme_page(
		__( 'Company Details', 'suburban-relocation' ),
		__( 'Company Details', 'suburban-relocation' ),
		'edit_theme_options',
		'srs-company-details',
		'srs_render_settings_page'
	);
}
add_action( 'admin_menu', 'srs_settings_menu' );

function srs_register_settings() {
	register_setting( 'srs_theme_settings', 'srs_theme_options', 'srs_sanitize_options' );
	add_settings_section( 'srs_contact_section', __( 'Contact and company information', 'suburban-relocation' ), 'srs_settings_intro', 'srs-company-details' );

	$fields = array(
		'phone'           => __( 'Main phone', 'suburban-relocation' ),
		'email'           => __( 'Public email', 'suburban-relocation' ),
		'hours'           => __( 'Business hours', 'suburban-relocation' ),
		'address'         => __( 'Main office address', 'suburban-relocation' ),
		'quote_recipient' => __( 'Quote notification email', 'suburban-relocation' ),
		'usdot'           => __( 'USDOT number', 'suburban-relocation' ),
		'mc'              => __( 'MC number', 'suburban-relocation' ),
		'txdot'           => __( 'TX DOT number', 'suburban-relocation' ),
	);
	foreach ( $fields as $key => $label ) {
		add_settings_field( $key, $label, 'srs_setting_field', 'srs-company-details', 'srs_contact_section', array( 'key' => $key ) );
	}
}
add_action( 'admin_init', 'srs_register_settings' );

function srs_settings_intro() {
	echo '<p>' . esc_html__( 'These values are used in the header, footer, article sidebar and quote notifications.', 'suburban-relocation' ) . '</p>';
}

function srs_setting_field( $args ) {
	$defaults = srs_option_defaults();
	$key      = $args['key'];
	$type     = in_array( $key, array( 'email', 'quote_recipient' ), true ) ? 'email' : 'text';
	?>
	<input class="regular-text" type="<?php echo esc_attr( $type ); ?>" name="srs_theme_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( srs_get_option( $key, $defaults[ $key ] ) ); ?>">
	<?php
}

function srs_option_defaults() {
	return array(
		'phone'           => '800-816-6834',
		'email'           => 'info@srelocation.com',
		'hours'           => 'Mon–Sun, 8am–8pm',
		'address'         => '12000 Old Baltimore Pike, Beltsville, MD 20705',
		'quote_recipient' => get_option( 'admin_email' ),
		'usdot'           => '2562098',
		'mc'              => '894071',
		'txdot'           => '007049119C',
	);
}

function srs_sanitize_options( $input ) {
	$defaults = srs_option_defaults();
	$output   = array();
	foreach ( $defaults as $key => $default ) {
		$value = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : $default;
		$output[ $key ] = in_array( $key, array( 'email', 'quote_recipient' ), true ) ? sanitize_email( $value ) : sanitize_text_field( $value );
	}
	return $output;
}

function srs_render_settings_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Suburban Relocation — Company Details', 'suburban-relocation' ); ?></h1>
		<form action="options.php" method="post">
			<?php settings_fields( 'srs_theme_settings' ); ?>
			<?php do_settings_sections( 'srs-company-details' ); ?>
			<?php submit_button(); ?>
		</form>
		<p><strong><?php esc_html_e( 'Logo:', 'suburban-relocation' ); ?></strong> <?php esc_html_e( 'Appearance → Customize → Site Identity.', 'suburban-relocation' ); ?></p>
		<p><strong><?php esc_html_e( 'Menus:', 'suburban-relocation' ); ?></strong> <?php esc_html_e( 'Appearance → Menus.', 'suburban-relocation' ); ?></p>
	</div>
	<?php
}
