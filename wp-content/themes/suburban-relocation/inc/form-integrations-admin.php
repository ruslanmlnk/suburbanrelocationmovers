<?php
/** Administrator-only settings and delivery audit. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'admin_menu', function() {
	add_submenu_page( 'edit.php?post_type=srs_lead', 'Интеграции форм', 'Интеграции форм', 'manage_options', 'srs-form-integrations', 'srs_form_integrations_page' );
} );
add_action( 'admin_init', function() {
	add_option( 'srs_form_integrations', array(), '', false );
	register_setting( 'srs_form_integrations', 'srs_form_integrations', array( 'sanitize_callback' => 'srs_sanitize_integrations', 'show_in_rest' => false ) );
} );

function srs_sanitize_integrations( $input ) {
	$input = is_array( $input ) ? $input : array();
	$output = srs_integration_options();
	foreach ( array( 'resend_from', 'recipient' ) as $key ) {
		$value = srs_quote_input( $input, $key );
		if ( '' === $value || is_email( $value ) ) { $output[ $key ] = sanitize_email( $value ); }
		else { add_settings_error( 'srs_form_integrations', $key, 'Некорректный email: ' . $key . '. Сохранено предыдущее значение.' ); }
	}
	$output['resend_name'] = sanitize_text_field( srs_quote_input( $input, 'resend_name' ) );
	$output['resend_name'] = str_replace( array( '<', '>' ), '', $output['resend_name'] ) ?: 'Suburban Relocation Systems';
	$output['granot_label'] = sanitize_text_field( srs_quote_input( $input, 'granot_label' ) );
	$output['granot_enabled'] = '1' === srs_quote_input( $input, 'granot_enabled' ) ? '1' : '0';
	$key = srs_quote_input( $input, 'resend_key' );
	if ( '1' === srs_quote_input( $input, 'remove_key' ) ) { $output['resend_key'] = ''; }
	elseif ( $key ) {
		if ( preg_match( '/^re_[A-Za-z0-9_-]+$/', $key ) ) { $output['resend_key'] = $key; }
		else { add_settings_error( 'srs_form_integrations', 'resend_key', 'Invalid API-ключ Resend. Previous key retained.' ); }
	}
	return $output;
}

function srs_delivery_label( $state ) {
	$labels = array( 'accepted' => 'Принято API', 'failed' => 'Ошибка', 'uncertain' => 'Требуется проверка', 'sending' => 'Отправляется / проверьте статус', 'configuration' => 'Требуется настройка', 'disabled' => 'Отключено' );
	return $labels[ $state ] ?? 'Ещё не отправлено';
}

function srs_form_integrations_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$options = srs_integration_options();
	?>
	<div class="wrap"><h1>Формы: Resend + Granot</h1>
	<p>Все формы сайта сохраняют заявки в <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=srs_lead' ) ); ?>">Quote Requests</a>. Статус каждого сервиса отображается отдельно.</p>
	<?php settings_errors(); ?>
	<form action="options.php" method="post">
		<?php settings_fields( 'srs_form_integrations' ); ?>
		<h2>Resend — уведомления о заявках</h2><table class="form-table" role="presentation">
		<tr><th><label for="srs-resend-key">API-ключ Resend</label></th><td>
		<input id="srs-resend-key" type="password" class="regular-text" name="srs_form_integrations[resend_key]" value="" autocomplete="new-password" placeholder="<?php echo srs_resend_key() ? 'Ключ настроен' : 're_…'; ?>">
		<p class="description">Пустое поле сохраняет текущий ключ. Также поддерживается SRS_RESEND_API_KEY в wp-config.php или переменных окружения (имеет приоритет).</p>
		<label><input type="checkbox" name="srs_form_integrations[remove_key]" value="1"> Удалить ключ из базы WordPress</label></td></tr>
		<?php foreach ( array( 'resend_from' => 'Email отправителя (подтверждённый домен Resend)', 'resend_name' => 'Имя отправителя', 'recipient' => 'Email для получения заявок' ) as $key => $label ) : ?>
		<tr><th><label for="srs-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th><td><input id="srs-<?php echo esc_attr( $key ); ?>" class="regular-text" type="<?php echo 'resend_name' === $key ? 'text' : 'email'; ?>" name="srs_form_integrations[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $options[ $key ] ); ?>"></td></tr>
		<?php endforeach; ?></table>
		<h2>Granot CRM</h2><table class="form-table" role="presentation">
		<tr><th>Отправка в CRM</th><td><label><input type="checkbox" name="srs_form_integrations[granot_enabled]" value="1" <?php checked( '1', $options['granot_enabled'] ); ?>> Включить Granot</label></td></tr>
		<tr><th><label for="srs-granot-label">Метка Granot (label)</label></th><td><input id="srs-granot-label" class="regular-text" name="srs_form_integrations[granot_label]" value="<?php echo esc_attr( $options['granot_label'] ); ?>"><p class="description">Точное название источника / компании, согласованное с Granot. Не копируйте label другой компании.</p></td></tr>
		<tr><th>Адрес API</th><td><code>https://api.starvanlinesmovers.com/Granot/send</code></td></tr></table>
		<?php submit_button( 'Сохранить настройки' ); ?>
	</form>
	<h2>Журнал отправки</h2><p>«Принято API» означает ответ сервиса, а не подтверждение доставки письма в почтовый ящик. После тайм-аута проверьте заявку в CRM перед повторной отправкой.</p>
	<?php
	$page = max( 1, absint( $_GET['paged'] ?? 1 ) );
	$selected = absint( $_GET['lead'] ?? 0 );
	$args = array( 'post_type' => 'srs_lead', 'post_status' => 'private', 'posts_per_page' => 20, 'paged' => $page );
	if ( $selected ) { $args['p'] = $selected; }
	$query = new WP_Query( $args );
	foreach ( $query->posts as $lead ) : ?>
	<div style="background:#fff;border:1px solid #ccd0d4;padding:18px;margin:16px 0;max-width:1100px">
	<h3><a href="<?php echo esc_url( get_edit_post_link( $lead->ID ) ); ?>"><?php echo esc_html( $lead->post_title ); ?></a></h3>
	<?php foreach ( array( 'resend' => 'Resend', 'granot' => 'Granot' ) as $provider => $name ) :
		$log = get_post_meta( $lead->ID, '_srs_delivery_' . $provider, true );
		$state = $log['state'] ?? '';
	?>
	<p><strong><?php echo esc_html( $name . ': ' . srs_delivery_label( $state ) ); ?></strong> — <?php echo esc_html( ( $log['time'] ?? '' ) . ' ' . ( $log['message'] ?? '' ) ); ?></p>
	<?php if ( 'accepted' !== $state ) : ?>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" style="margin-bottom:16px">
		<input type="hidden" name="action" value="srs_retry_delivery"><input type="hidden" name="lead" value="<?php echo esc_attr( $lead->ID ); ?>"><input type="hidden" name="provider" value="<?php echo esc_attr( $provider ); ?>">
		<?php wp_nonce_field( 'srs_retry_' . $lead->ID . '_' . $provider ); ?>
		<label><input type="checkbox" name="confirmed" value="1" required> Проверено в сервисе: эту заявку нужно отправить.</label>
		<button class="button" type="submit"><?php echo esc_html( 'Отправить в ' . $name ); ?></button>
	</form>
	<?php endif; endforeach; ?>
	<details><summary>Данные заявки и история попыток</summary><pre style="white-space:pre-wrap;overflow-wrap:anywhere"><?php echo esc_html( wp_json_encode( array( 'request' => srs_quote_data( $lead->ID ), 'granot_payload' => get_post_meta( $lead->ID, '_srs_granot_payload', true ), 'history' => get_post_meta( $lead->ID, '_srs_delivery_history', true ) ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ); ?></pre></details></div>
	<?php endforeach;
	if ( ! $query->posts ) { echo '<p>Заявок пока нет.</p>'; }
	if ( ! $selected ) { echo wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'paged', '%#%' ), 'format' => '', 'current' => $page, 'total' => $query->max_num_pages ) ) ); }
	?></div><?php
}

add_action( 'admin_post_srs_retry_delivery', function() {
	if ( ! current_user_can( 'manage_options' ) || 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) { wp_die( 'Доступ запрещён', '', array( 'response' => 403 ) ); }
	$lead = absint( $_POST['lead'] ?? 0 );
	$provider = srs_quote_input( wp_unslash( $_POST ), 'provider' );
	check_admin_referer( 'srs_retry_' . $lead . '_' . $provider );
	if ( ! in_array( $provider, array( 'resend', 'granot' ), true ) || '1' !== srs_quote_input( $_POST, 'confirmed' ) ) { wp_die( 'Сначала подтвердите проверку заявки в сервисе.' ); }
	srs_deliver_quote( $lead, $provider, true );
	wp_safe_redirect( admin_url( 'edit.php?post_type=srs_lead&page=srs-form-integrations&lead=' . $lead ) );
	exit;
} );

add_filter( 'manage_srs_lead_posts_columns', function( $columns ) {
	$columns['srs_delivery'] = 'Resend / Granot';
	return $columns;
} );
add_action( 'manage_srs_lead_posts_custom_column', function( $column, $lead ) {
	if ( 'srs_delivery' !== $column || ! current_user_can( 'manage_options' ) ) { return; }
	foreach ( array( 'resend', 'granot' ) as $provider ) {
		$log = get_post_meta( $lead, '_srs_delivery_' . $provider, true );
		echo esc_html( ucfirst( $provider ) . ': ' . srs_delivery_label( $log['state'] ?? '' ) ) . '<br>';
	}
	echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=srs_lead&page=srs-form-integrations&lead=' . $lead ) ) . '">Журнал / повторная отправка</a>';
}, 10, 2 );

add_action( 'admin_notices', function() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$options = srs_integration_options();
	if ( ! srs_resend_key() || ! is_email( $options['resend_from'] ) || ! is_email( $options['recipient'] ) || ( '1' === $options['granot_enabled'] && ! $options['granot_label'] ) ) {
		echo '<div class="notice notice-warning"><p>Заявки сохраняются в WordPress, но интеграции форм ещё не полностью настроены. <a href="' . esc_url( admin_url( 'edit.php?post_type=srs_lead&page=srs-form-integrations' ) ) . '">Настроить Resend / Granot</a></p></div>';
	}
} );
