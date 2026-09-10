<?php
/** Isolated contract tests: all HTTP calls are mocked; no real leads or emails are sent. */
if ( PHP_SAPI !== 'cli' ) { exit; }
define( 'ABSPATH', __DIR__ );
define( 'DAY_IN_SECONDS', 86400 );
define( 'MINUTE_IN_SECONDS', 60 );
class WP_Error {
	public $code; public $message;
	function __construct( $code, $message ) { $this->code = $code; $this->message = $message; }
	function get_error_message() { return $this->message; }
}
function is_wp_error( $v ) { return $v instanceof WP_Error; }
function add_action( ...$v ) {}
function add_filter( ...$v ) {}
function wp_parse_args( $a, $b ) { return array_merge( $b, $a ); }
function srs_get_option( $k, $d = '' ) { return $d; }
function get_option( $k, $d = false ) { return $GLOBALS['options'][$k] ?? $d; }
function add_option( $k, $v, ...$args ) { if ( isset( $GLOBALS['options'][$k] ) ) return false; $GLOBALS['options'][$k] = $v; return true; }
function update_option( $k, $v, ...$args ) { $GLOBALS['options'][$k] = $v; return true; }
function delete_option( $k ) { unset( $GLOBALS['options'][$k] ); }
function wp_slash( $v ) { return $v; }
function wp_json_encode( $v, $flags = 0 ) { return json_encode( $v, $flags ); }
function wp_salt( $v ) { return 'test-salt'; }
function wp_schedule_single_event( ...$args ) { return true; }
function wp_insert_post( $v, $e ) { if ( ! empty( $GLOBALS['storage_fail'] ) ) return new WP_Error( 'db', 'failure' ); $id = count( $GLOBALS['posts'] ) + 1; $GLOBALS['posts'][$id] = $v; return $id; }
function get_post_type( $id ) { return $GLOBALS['posts'][$id]['post_type'] ?? ''; }
function get_post_status( $id ) { return $GLOBALS['posts'][$id]['post_status'] ?? ''; }
function get_post_field( $key, $id ) { return $GLOBALS['posts'][$id][$key] ?? ''; }
function get_post_meta( $id, $key, $single = true ) { return $GLOBALS['meta'][$id][$key] ?? ''; }
function update_post_meta( $id, $key, $v ) { $GLOBALS['meta'][$id][$key] = $v; return true; }
function sanitize_text_field( $v ) { return trim( strip_tags( $v ) ); }
function sanitize_textarea_field( $v ) { return trim( strip_tags( $v ) ); }
function sanitize_email( $v ) { return filter_var( $v, FILTER_SANITIZE_EMAIL ); }
function is_email( $v ) { return filter_var( $v, FILTER_VALIDATE_EMAIL ); }
function wp_timezone() { return new DateTimeZone( 'UTC' ); }
function wp_date( $v ) { return gmdate( $v ); }
function current_time( $v ) { return gmdate( 'Y-m-d H:i:s' ); }
function home_url( $path = '' ) { return 'https://example.com' . $path; }
function wp_parse_url( $v, $component ) { return parse_url( $v, $component ); }
function esc_url_raw( $v ) { return $v; }
function remove_query_arg( $args, $v ) { return $v; }
function esc_html( $v ) { return htmlspecialchars( $v, ENT_QUOTES ); }
function wp_generate_uuid4() { static $i=0; return 'test-uuid-' . ++$i; }
function wp_remote_retrieve_response_code( $v ) { return $v['response']['code']; }
function wp_remote_retrieve_body( $v ) { return $v['body']; }
function wp_remote_post( $url, $args ) { $GLOBALS['calls'][] = array( $url, $args ); return array_shift( $GLOBALS['responses'] ); }
function add_settings_error( ...$args ) { $GLOBALS['settings_errors'][] = $args; }
function check( $condition, $message ) { if ( ! $condition ) throw new RuntimeException( $message ); echo "PASS: $message\n"; }
function response( $code, $body ) { return array( 'response' => array( 'code' => $code ), 'body' => $body ); }
$options = $posts = $meta = $calls = $responses = array();
require __DIR__ . '/../wp-content/themes/suburban-relocation/inc/form-handler.php';
require __DIR__ . '/../wp-content/themes/suburban-relocation/inc/form-integrations.php';
require __DIR__ . '/../wp-content/themes/suburban-relocation/inc/form-integrations-admin.php';
$input = array( 'full_name' => "O'Neil & Sons", 'phone' => '+1 (202) 555-0100', 'email' => 'test@example.com', 'move_date' => '2099-12-01', 'moving_from' => '00123', 'moving_to' => '90210', 'source_url' => 'https://example.com/local.html' );
$data = srs_validate_quote( $input );
check( ! is_wp_error( $data ) && $data['moving_from'] === '00123', 'valid request and leading-zero ZIP retained' );
foreach ( array( 'phone' => 'abc', 'email' => 'bad-email', 'move_date' => '2099-02-30' ) as $key => $bad ) {
	check( is_wp_error( srs_validate_quote( array_merge( $input, array( $key => $bad ) ) ) ), "reject invalid $key" );
}
check( is_wp_error( srs_validate_quote( array_merge( $input, array( 'phone' => array( 'x' ) ) ) ) ), 'reject array input without crashing' );
check( srs_validate_quote( array_merge( $input, array( 'source_url' => 'https://evil.example/' ) ) )['source_url'] === home_url( '/' ), 'untrusted source host rejected' );
$stored = srs_store_quote( $data ); $lead = $stored['id'];
check( srs_quote_data( $lead ) === $data, 'full lead payload persists before delivery' );
check( srs_store_quote( $data )['duplicate'] && count( $posts ) === 1, 'duplicate submission reuses lead' );
$storage_fail = true;
check( is_wp_error( srs_store_quote( array_merge( $data, array( 'phone' => '2025550199' ) ) ) ), 'storage failure is not success' );
$storage_fail = false;
$options['srs_form_integrations'] = array( 'granot_enabled' => '0' );
srs_deliver_quote( $lead );
check( count( $calls ) === 0 && get_post_meta( $lead, '_srs_delivery_resend' )['state'] === 'configuration', 'missing settings retain lead without API calls' );
$options['srs_form_integrations'] = array( 'resend_key' => 're_testkey', 'resend_from' => 'quotes@example.com', 'recipient' => 'owner@example.com', 'granot_enabled' => '1', 'granot_label' => 'TEST_LABEL' );
$responses = array( response( 403, '{"message":"invalid sender"}' ), response( 200, '{"success":true}' ) );
srs_deliver_quote( $lead );
check( count( $calls ) === 2 && get_post_meta( $lead, '_srs_delivery_granot' )['state'] === 'accepted', 'Granot still sent after Resend failure' );
check( $calls[0][0] === 'https://api.resend.com/emails' && $calls[0][1]['headers']['Authorization'] === 'Bearer re_testkey', 'Resend endpoint and bearer authentication' );
$mail = json_decode( $calls[0][1]['body'], true );
check( $mail['reply_to'] === $data['email'] && $mail['to'] === array( 'owner@example.com' ) && str_contains( $mail['html'], 'O&#039;Neil &amp; Sons' ), 'recipient, reply-to and HTML escaping' );
parse_str( $calls[1][1]['body'], $granot );
check( $granot === srs_granot_payload( $data, 'TEST_LABEL' ) && $granot['ozip'] === '00123', 'Granot form encoding and complete field mapping' );
$options['srs_form_integrations']['resend_from'] = 'corrected@example.com';
$responses = array( response( 200, '{"id":"email-123"}' ) );
srs_deliver_quote( $lead, 'resend', true );
check( count( $calls ) === 3 && json_decode( $calls[2][1]['body'], true )['from'] === 'Suburban Relocation Systems <corrected@example.com>', 'retry rejected email uses corrected sender and skips accepted CRM' );
srs_deliver_quote( $lead, '', true );
check( count( $calls ) === 3, 'accepted channels never resend' );
$second = srs_store_quote( array_merge( $data, array( 'phone' => '2025550198' ) ) )['id'];
$responses = array( new WP_Error( 'timeout', 'secret re_testkey' ), new WP_Error( 'timeout', 'timeout' ) );
srs_deliver_quote( $second );
check( get_post_meta( $second, '_srs_delivery_resend' )['state'] === 'uncertain' && get_post_meta( $second, '_srs_delivery_granot' )['state'] === 'uncertain', 'timeouts are uncertain, not falsely accepted' );
$first_attempt = $calls[3][1];
$responses = array( response( 200, '{"id":"email-456"}' ) );
srs_deliver_quote( $second, 'resend', true );
check( $first_attempt['headers']['Idempotency-Key'] === $calls[5][1]['headers']['Idempotency-Key'] && $first_attempt['body'] === $calls[5][1]['body'], 'uncertain Resend retry uses identical payload and idempotency key' );
check( ! str_contains( json_encode( get_post_meta( $second, '_srs_delivery_history' ) ), 're_testkey' ), 'secret absent from delivery history' );
check( srs_delivery_result( 'granot', response( 200, '{"success":false}' ) )['state'] === 'failed', 'Granot 200 with business error is failure' );
check( srs_delivery_result( 'granot', response( 200, 'false' ) )['state'] === 'failed', 'Granot literal false is failure' );
check( srs_delivery_result( 'resend', response( 200, '{}' ) )['state'] === 'uncertain', 'Resend requires email ID' );
$clean = srs_sanitize_integrations( array( 'resend_key' => '', 'resend_from' => 'quotes@example.com', 'recipient' => 'owner@example.com' ) );
check( $clean['resend_key'] === 're_testkey', 'blank key preserves stored credential' );
check( srs_sanitize_integrations( array( 'remove_key' => '1' ) )['resend_key'] === '', 'explicit credential removal' );
$third = srs_store_quote( array_merge( $data, array( 'phone' => '2025550197' ) ) )['id'];
add_option( 'srs_delivery_lock_' . $third . '_resend', time() );
$before = count( $calls );
srs_deliver_quote( $third, 'resend', true );
check( count( $calls ) === $before, 'active delivery lock blocks concurrent retry' );
delete_option( 'srs_delivery_lock_' . $third . '_resend' );
$responses = array( response( 500, '{"error":"re_testkey"}' ) );
srs_deliver_quote( $third, 'resend' );
check( ! str_contains( json_encode( get_post_meta( $third, '_srs_delivery_history' ) ), 're_testkey' ), 'API response body redacts credential' );
$envelope = get_post_meta( $third, '_srs_resend_envelope' );
$envelope['created'] -= DAY_IN_SECONDS + 1;
update_post_meta( $third, '_srs_resend_envelope', $envelope );
$before = count( $calls );
srs_deliver_quote( $third, 'resend' );
check( count( $calls ) === $before, 'uncertain delivery never retries without review' );
$responses = array( response( 200, '{"id":"after-review"}' ) );
srs_deliver_quote( $third, 'resend', true );
check( get_post_meta( $third, '_srs_resend_envelope' )['key'] !== $envelope['key'], 'confirmed retry after 24 hours creates a new key' );
echo "All integration contract tests passed. No network requests were made.\n";
