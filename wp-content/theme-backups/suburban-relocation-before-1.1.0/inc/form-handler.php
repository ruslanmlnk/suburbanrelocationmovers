<?php
/** Quote request processing. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function srs_handle_quote_request() {
	if ( ! isset( $_POST['srs_quote_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['srs_quote_nonce'] ) ), 'srs_quote_request' ) ) {
		wp_die( esc_html__( 'This form session expired. Please go back and try again.', 'suburban-relocation' ), esc_html__( 'Form expired', 'suburban-relocation' ), array( 'response' => 403 ) );
	}

	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$redirect = remove_query_arg( 'srs_quote', $redirect );

	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'srs_quote', 'success', $redirect ) . '#quote' );
		exit;
	}

	$data = array(
		'full_name'   => isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '',
		'phone'       => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
		'move_date'   => isset( $_POST['move_date'] ) ? sanitize_text_field( wp_unslash( $_POST['move_date'] ) ) : '',
		'email'       => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
		'moving_from' => isset( $_POST['moving_from'] ) ? sanitize_text_field( wp_unslash( $_POST['moving_from'] ) ) : '',
		'moving_to'   => isset( $_POST['moving_to'] ) ? sanitize_text_field( wp_unslash( $_POST['moving_to'] ) ) : '',
		'source_url'  => isset( $_POST['source_url'] ) ? esc_url_raw( wp_unslash( $_POST['source_url'] ) ) : $redirect,
	);

	if ( '' === $data['phone'] ) {
		wp_safe_redirect( add_query_arg( 'srs_quote', 'missing-phone', $redirect ) . '#quote' );
		exit;
	}

	$title = sprintf(
		/* translators: 1: customer identifier, 2: submission date. */
		__( 'Quote: %1$s — %2$s', 'suburban-relocation' ),
		$data['full_name'] ? $data['full_name'] : $data['phone'],
		wp_date( 'M j, Y g:i a' )
	);
	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'srs_lead',
			'post_status' => 'private',
			'post_title'  => $title,
		),
		true
	);

	if ( ! is_wp_error( $lead_id ) ) {
		$meta_map = array(
			'_srs_full_name' => 'full_name',
			'_srs_phone'     => 'phone',
			'_srs_move_date' => 'move_date',
			'_srs_email'     => 'email',
			'_srs_from'      => 'moving_from',
			'_srs_to'        => 'moving_to',
			'_srs_source'    => 'source_url',
		);
		foreach ( $meta_map as $meta_key => $data_key ) {
			update_post_meta( $lead_id, $meta_key, $data[ $data_key ] );
		}
	}

	$recipient = srs_get_option( 'quote_recipient', get_option( 'admin_email' ) );
	$subject   = sprintf( __( 'New moving quote request: %s', 'suburban-relocation' ), $data['phone'] );
	$message   = implode(
		"\n",
		array(
			'Full name: ' . ( $data['full_name'] ? $data['full_name'] : '—' ),
			'Phone: ' . $data['phone'],
			'Move date: ' . ( $data['move_date'] ? $data['move_date'] : '—' ),
			'Email: ' . ( $data['email'] ? $data['email'] : '—' ),
			'Moving from: ' . ( $data['moving_from'] ? $data['moving_from'] : '—' ),
			'Moving to: ' . ( $data['moving_to'] ? $data['moving_to'] : '—' ),
			'Source: ' . $data['source_url'],
		)
	);
	$headers = array();
	if ( $data['email'] ) {
		$headers[] = 'Reply-To: ' . $data['email'];
	}
	wp_mail( $recipient, $subject, $message, $headers );

	wp_safe_redirect( add_query_arg( 'srs_quote', 'success', $redirect ) . '#quote' );
	exit;
}
add_action( 'admin_post_nopriv_srs_submit_quote', 'srs_handle_quote_request' );
add_action( 'admin_post_srs_submit_quote', 'srs_handle_quote_request' );
