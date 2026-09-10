<?php
/** Shared intake for every quote form: validate, persist, then deliver. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function srs_quote_input( $input, $key ) {
	return isset( $input[ $key ] ) && is_string( $input[ $key ] ) ? trim( $input[ $key ] ) : '';
}

function srs_validate_quote( $input ) {
	$data = array();
	foreach ( array( 'full_name', 'phone', 'move_date', 'email', 'moving_from', 'moving_to', 'source_url' ) as $key ) {
		$raw = srs_quote_input( $input, $key );
		if ( strlen( $raw ) > ( 'source_url' === $key ? 2000 : 200 ) ) {
			return new WP_Error( 'invalid', 'Please shorten the information in the form.' );
		}
		$data[ $key ] = sanitize_text_field( $raw );
	}
	$digits = preg_replace( '/\D/', '', $data['phone'] );
	if ( strlen( $digits ) < 7 || strlen( $digits ) > 15 || ! preg_match( '/^[+()\d\s.\-]+$/', $data['phone'] ) ) {
		return new WP_Error( 'invalid-phone', 'Please enter a valid phone number.' );
	}
	if ( '' !== $data['email'] && ! is_email( $data['email'] ) ) {
		return new WP_Error( 'invalid-email', 'Please enter a valid email address.' );
	}
	$data['email'] = sanitize_email( $data['email'] );
	if ( $data['move_date'] ) {
		$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $data['move_date'], wp_timezone() );
		if ( ! $date || $date->format( 'Y-m-d' ) !== $data['move_date'] || $data['move_date'] < wp_date( 'Y-m-d' ) ) {
			return new WP_Error( 'invalid-date', 'Please choose today or a future move date.' );
		}
	}
	$source = esc_url_raw( $data['source_url'] );
	$data['source_url'] = wp_parse_url( $source, PHP_URL_HOST ) === wp_parse_url( home_url(), PHP_URL_HOST ) ? $source : home_url( '/' );
	$data['source_url'] = remove_query_arg( array( 'srs_quote', '_wpnonce' ), $data['source_url'] );
	return $data;
}

function srs_store_quote( $data ) {
	// Reserve a fingerprint atomically to suppress double clicks and network retries.
	$key = 'srs_quote_' . hash_hmac( 'sha256', wp_json_encode( $data ), wp_salt( 'nonce' ) );
	$reservation = get_option( $key );
	if ( is_array( $reservation ) && $reservation['time'] < time() - 600 ) { delete_option( $key ); }
	if ( ! add_option( $key, array( 'time' => time(), 'lead' => 0 ), '', false ) ) {
		$reservation = get_option( $key );
		if ( ! empty( $reservation['lead'] ) && 'srs_lead' === get_post_type( $reservation['lead'] ) ) {
			return array( 'id' => (int) $reservation['lead'], 'duplicate' => true );
		}
		return new WP_Error( 'processing', 'Your request is being saved. Please wait a moment and try again.' );
	}
	wp_schedule_single_event( time() + 601, 'srs_quote_reservation_cleanup', array( $key ) );
	$lead = wp_insert_post( array(
		'post_type' => 'srs_lead', 'post_status' => 'private',
		'post_title' => wp_slash( 'Quote: ' . ( $data['full_name'] ?: $data['phone'] ) . ' — ' . wp_date( 'M j, Y g:i a' ) ),
		// Persist the complete payload with the lead, even if later meta writes fail.
		'post_content' => wp_slash( wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) ),
	), true );
	if ( is_wp_error( $lead ) || ! $lead ) {
		delete_option( $key );
		return new WP_Error( 'storage', 'We could not save your request. Please try again or call us.' );
	}
	$map = array( 'full_name' => 'full_name', 'phone' => 'phone', 'move_date' => 'move_date', 'email' => 'email', 'moving_from' => 'from', 'moving_to' => 'to', 'source_url' => 'source' );
	foreach ( $map as $field => $meta ) { update_post_meta( $lead, '_srs_' . $meta, wp_slash( $data[ $field ] ) ); }
	update_option( $key, array( 'time' => time(), 'lead' => $lead ), false );
	return array( 'id' => $lead, 'duplicate' => false );
}
add_action( 'srs_quote_reservation_cleanup', function( $key ) {
	$value = get_option( $key );
	if ( is_array( $value ) && $value['time'] <= time() - 600 ) { delete_option( $key ); }
} );

function srs_handle_quote_request() {
	$input = wp_unslash( $_POST );
	$error = null;
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! wp_verify_nonce( srs_quote_input( $input, 'srs_quote_nonce' ), 'srs_quote_request' ) ) {
		$error = new WP_Error( 'expired', 'Your form session expired. Please refresh this page and try again.' );
	} elseif ( srs_quote_input( $input, 'website' ) ) {
		srs_quote_response();
	} else {
		$data = srs_validate_quote( $input );
		if ( is_wp_error( $data ) ) {
			$error = $data;
		} else {
			$rate_key = 'srs_quote_rate_' . hash_hmac( 'sha256', (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ), wp_salt( 'nonce' ) );
			$count = (int) get_transient( $rate_key );
			if ( $count >= 10 ) {
				$error = new WP_Error( 'rate-limit', 'Too many requests. Please wait a few minutes or call us.' );
			} else {
				$stored = srs_store_quote( $data );
				if ( is_wp_error( $stored ) ) {
					$error = $stored;
				} elseif ( ! $stored['duplicate'] ) {
					set_transient( $rate_key, $count + 1, 10 * MINUTE_IN_SECONDS );
					srs_deliver_quote( $stored['id'] );
				}
			}
		}
	}
	srs_quote_response( $error );
}

function srs_quote_response( $error = null ) {
	if ( wp_doing_ajax() ) {
		if ( $error ) { wp_send_json_error( array( 'message' => $error->get_error_message() ), 422 ); }
		wp_send_json_success( array( 'message' => 'Thank you! Your request has been received. A moving coordinator will contact you shortly.' ) );
	}
	if ( $error ) { wp_die( esc_html( $error->get_error_message() ), 'Request not submitted', array( 'response' => 422, 'back_link' => true ) ); }
	$redirect = wp_validate_redirect( wp_get_referer(), home_url( '/' ) );
	$redirect = preg_replace( '/#.*$/', '', remove_query_arg( 'srs_quote', $redirect ) );
	wp_safe_redirect( add_query_arg( 'srs_quote', 'success', $redirect ) . '#quote' );
	exit;
}
add_action( 'admin_post_nopriv_srs_submit_quote', 'srs_handle_quote_request' );
add_action( 'admin_post_srs_submit_quote', 'srs_handle_quote_request' );
add_action( 'wp_ajax_nopriv_srs_submit_quote', 'srs_handle_quote_request' );
add_action( 'wp_ajax_srs_submit_quote', 'srs_handle_quote_request' );

function srs_quote_fresh_nonce() {
	nocache_headers();
	wp_send_json_success( array( 'nonce' => wp_create_nonce( 'srs_quote_request' ) ) );
}
add_action( 'wp_ajax_nopriv_srs_quote_nonce', 'srs_quote_fresh_nonce' );
add_action( 'wp_ajax_srs_quote_nonce', 'srs_quote_fresh_nonce' );
