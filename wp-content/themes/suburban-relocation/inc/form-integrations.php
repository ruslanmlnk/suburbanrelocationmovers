<?php
/** Server-side delivery adapters. Secrets never enter frontend HTML or logs. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function srs_integration_options() {
	return wp_parse_args( get_option( 'srs_form_integrations', array() ), array(
		'resend_key' => '', 'resend_from' => '', 'resend_name' => 'Suburban Relocation Systems',
		'recipient' => srs_get_option( 'quote_recipient', get_option( 'admin_email' ) ),
		'granot_enabled' => '1', 'granot_label' => 'suburbanrelocationmovers',
	) );
}

function srs_resend_key() {
	if ( defined( 'SRS_RESEND_API_KEY' ) ) { return (string) SRS_RESEND_API_KEY; }
	return getenv( 'SRS_RESEND_API_KEY' ) ?: srs_integration_options()['resend_key'];
}

function srs_quote_data( $lead ) {
	$data = json_decode( get_post_field( 'post_content', $lead ), true );
	if ( is_array( $data ) && isset( $data['phone'] ) ) { return $data; }
	$data = array();
	foreach ( array( 'full_name' => 'full_name', 'phone' => 'phone', 'move_date' => 'move_date', 'email' => 'email', 'moving_from' => 'from', 'moving_to' => 'to', 'source_url' => 'source' ) as $field => $meta ) {
		$data[ $field ] = get_post_meta( $lead, '_srs_' . $meta, true );
	}
	return $data;
}

function srs_granot_payload( $data, $label ) {
	return array(
		'firstname' => $data['full_name'], 'email' => $data['email'], 'phone1' => $data['phone'],
		'ozip' => $data['moving_from'], 'dzip' => $data['moving_to'], 'movedte' => $data['move_date'],
		'movesize' => '', 'notes' => 'Source: ' . $data['source_url'], 'label' => $label,
	);
}

function srs_resend_payload( $data, $settings ) {
	$lines = array( 'New moving quote request', '' );
	foreach ( array( 'full_name' => 'Full name', 'phone' => 'Phone', 'email' => 'Email', 'move_date' => 'Move date', 'moving_from' => 'Moving from', 'moving_to' => 'Moving to', 'source_url' => 'Source page' ) as $key => $label ) {
		$lines[] = $label . ': ' . ( $data[ $key ] ?: '—' );
	}
	$text = implode( "\n", $lines );
	$payload = array(
		'from' => $settings['resend_name'] . ' <' . $settings['resend_from'] . '>',
		'to' => array( $settings['recipient'] ),
		'subject' => 'New moving quote request: ' . $data['phone'],
		'text' => $text,
		'html' => '<div style="font-family:Arial,sans-serif;line-height:1.6;max-width:640px"><h1 style="font-size:24px">New moving quote request</h1><p>' . nl2br( esc_html( implode( "\n", array_slice( $lines, 2 ) ) ) ) . '</p></div>',
	);
	if ( $data['email'] ) { $payload['reply_to'] = $data['email']; }
	return $payload;
}

function srs_delivery_result( $provider, $response ) {
	if ( is_wp_error( $response ) ) {
		return array( 'state' => 'uncertain', 'code' => 0, 'message' => 'Ошибка сети. Сервис мог получить заявку. Проверьте её в личном кабинете перед повторной отправкой.' );
	}
	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( $code < 200 || $code >= 300 ) {
		return array( 'state' => $code >= 500 || 409 === $code ? 'uncertain' : 'failed', 'code' => $code, 'message' => 'API вернул HTTP ' . $code . '. Проверьте ключ доступа, домен отправителя, лимиты аккаунта и настройки интеграции.' );
	}
	if ( 'resend' === $provider ) {
		if ( ! is_array( $body ) || empty( $body['id'] ) || ! is_string( $body['id'] ) ) {
			return array( 'state' => 'uncertain', 'code' => $code, 'message' => 'Resend не вернул ID письма. Проверьте статус в личном кабинете Resend.' );
		}
		return array( 'state' => 'accepted', 'code' => $code, 'message' => 'Принято Resend (доставка письма не подтверждена).', 'remote_id' => sanitize_text_field( $body['id'] ) );
	}
	// The supplied Granot gateway has no documented success schema. Preserve that distinction.
	if ( false === $body || ( is_array( $body ) && ( ! empty( $body['error'] ) || ( isset( $body['success'] ) && in_array( $body['success'], array( false, 0, 'false', '0' ), true ) ) ) ) ) {
		return array( 'state' => 'failed', 'code' => $code, 'message' => 'Шлюз Granot сообщил об ошибке. Проверьте CRM перед повторной отправкой.' );
	}
	return array( 'state' => 'accepted', 'code' => $code, 'message' => 'Шлюз Granot вернул HTTP ' . $code . '. Проверьте наличие заявки в CRM.' );
}

function srs_delivery_log( $lead, $provider, $result ) {
	$result['time'] = current_time( 'mysql' );
	$previous = get_post_meta( $lead, '_srs_delivery_' . $provider, true );
	$result['attempts'] = (int) ( $previous['attempts'] ?? 0 ) + ( ! empty( $result['attempted'] ) ? 1 : 0 );
	update_post_meta( $lead, '_srs_delivery_' . $provider, wp_slash( $result ) );
	$history = get_post_meta( $lead, '_srs_delivery_history', true );
	$history = is_array( $history ) ? $history : array();
	$history[] = array_merge( array( 'provider' => $provider ), $result );
	update_post_meta( $lead, '_srs_delivery_history', wp_slash( array_slice( $history, -30 ) ) );
}

function srs_deliver_quote( $lead, $only = '', $confirmed = false ) {
	if ( 'srs_lead' !== get_post_type( $lead ) || 'private' !== get_post_status( $lead ) ) { return; }
	$settings = srs_integration_options();
	$data = srs_quote_data( $lead );
	foreach ( array( 'resend', 'granot' ) as $provider ) {
		if ( $only && $only !== $provider ) { continue; }
		$previous = get_post_meta( $lead, '_srs_delivery_' . $provider, true );
		if ( 'accepted' === ( $previous['state'] ?? '' ) ) { continue; }
		if ( in_array( $previous['state'] ?? '', array( 'sending', 'uncertain' ), true ) && ! $confirmed ) { continue; }
		$lock = 'srs_delivery_lock_' . $lead . '_' . $provider;
		if ( $confirmed && (int) get_option( $lock ) < time() - 120 ) { delete_option( $lock ); }
		if ( ! add_option( $lock, time(), '', false ) ) { continue; }
		try {
			if ( 'granot' === $provider && '1' !== $settings['granot_enabled'] ) {
				srs_delivery_log( $lead, $provider, array( 'state' => 'disabled', 'message' => 'Granot отключён в настройках интеграций форм.' ) );
				continue;
			}
			if ( ( 'resend' === $provider && ( ! srs_resend_key() || ! is_email( $settings['resend_from'] ) || ! is_email( $settings['recipient'] ) ) ) || ( 'granot' === $provider && ! $settings['granot_label'] ) ) {
				srs_delivery_log( $lead, $provider, array( 'state' => 'configuration', 'message' => 'Заполните настройки в Quote Requests → Интеграции форм, затем отправьте сохранённую заявку.' ) );
				continue;
			}
			if ( 'resend' === $provider ) {
				// Keep an immutable payload and UUID for every retry within Resend's 24-hour window.
				$envelope = get_post_meta( $lead, '_srs_resend_envelope', true );
				if ( 'failed' === ( $previous['state'] ?? '' ) && in_array( (int) ( $previous['code'] ?? 0 ), array( 400, 401, 403, 404, 422 ), true ) ) {
					// Explicit rejection: allow corrected sender / recipient settings on the next attempt.
					$envelope = null;
				}
				if ( ! is_array( $envelope ) || ( time() - $envelope['created'] >= DAY_IN_SECONDS ) ) {
					if ( is_array( $envelope ) && ! $confirmed ) {
						srs_delivery_log( $lead, $provider, array( 'state' => 'uncertain', 'message' => '24-часовой период защиты от дубликатов истёк. Проверьте Resend перед подтверждением новой попытки.' ) );
						continue;
					}
					$envelope = array( 'created' => time(), 'key' => 'srs-quote/' . wp_generate_uuid4(), 'payload' => srs_resend_payload( $data, $settings ) );
					if ( ! update_post_meta( $lead, '_srs_resend_envelope', wp_slash( $envelope ) ) ) { continue; }
				}
				$url = 'https://api.resend.com/emails';
				$args = array( 'timeout' => 10, 'redirection' => 0, 'headers' => array( 'Authorization' => 'Bearer ' . srs_resend_key(), 'Content-Type' => 'application/json', 'Idempotency-Key' => $envelope['key'] ), 'body' => wp_json_encode( $envelope['payload'] ) );
			} else {
				$url = 'https://api.starvanlinesmovers.com/Granot/send';
				$payload = srs_granot_payload( $data, $settings['granot_label'] );
				update_post_meta( $lead, '_srs_granot_payload', wp_slash( $payload ) );
				$args = array( 'timeout' => 15, 'redirection' => 0, 'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ), 'body' => http_build_query( $payload, '', '&' ) );
			}
			srs_delivery_log( $lead, $provider, array( 'state' => 'sending', 'message' => 'Заявка отправляется. Если статус не меняется, проверьте её в сервисе перед повторной отправкой.' ) );
			$args['limit_response_size'] = 16000;
			$response = wp_remote_post( $url, $args );
			$result = srs_delivery_result( $provider, $response );
			if ( ! is_wp_error( $response ) ) {
				$body = wp_remote_retrieve_body( $response );
				if ( srs_resend_key() ) { $body = str_replace( srs_resend_key(), '[redacted]', $body ); }
				$result['response'] = sanitize_textarea_field( substr( preg_replace( '/re_[A-Za-z0-9_-]+/', '[redacted]', $body ), 0, 4000 ) );
			}
			$result['attempted'] = true;
			srs_delivery_log( $lead, $provider, $result );
		} catch ( Throwable $error ) {
			srs_delivery_log( $lead, $provider, array( 'state' => 'uncertain', 'message' => 'Отправка прервана. Проверьте статус в сервисе перед повторной отправкой.', 'attempted' => true ) );
		} finally {
			delete_option( $lock );
		}
	}
}
