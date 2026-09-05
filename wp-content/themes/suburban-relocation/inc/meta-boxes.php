<?php
/** Editor side panels and admin columns. */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function srs_add_meta_boxes() {
	add_meta_box( 'srs_service_details', __( 'Service card details', 'suburban-relocation' ), 'srs_service_meta_box', 'srs_service', 'side', 'default' );
	add_meta_box( 'srs_location_details', __( 'Location details', 'suburban-relocation' ), 'srs_location_meta_box', 'srs_location', 'side', 'default' );
	add_meta_box( 'srs_city_details', __( 'City details', 'suburban-relocation' ), 'srs_city_meta_box', 'srs_city', 'side', 'default' );
	add_meta_box( 'srs_lead_details', __( 'Request details', 'suburban-relocation' ), 'srs_lead_meta_box', 'srs_lead', 'normal', 'high' );
}

function srs_city_meta_box( $post ) {
	wp_nonce_field( 'srs_save_meta', 'srs_meta_nonce' );
	$parent_state = absint( get_post_meta( $post->ID, '_srs_parent_state', true ) );
	$states = get_posts( array( 'post_type' => 'srs_location', 'post_status' => array( 'publish', 'draft' ), 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	?>
	<p><label for="srs_parent_state"><strong><?php esc_html_e( 'Parent state', 'suburban-relocation' ); ?></strong></label></p>
	<select class="widefat" id="srs_parent_state" name="srs_parent_state" required>
		<option value=""><?php esc_html_e( 'Select a state', 'suburban-relocation' ); ?></option>
		<?php foreach ( $states as $state ) : ?><option value="<?php echo esc_attr( $state->ID ); ?>" <?php selected( $parent_state, $state->ID ); ?>><?php echo esc_html( $state->post_title ); ?></option><?php endforeach; ?>
	</select>
	<p class="description"><?php esc_html_e( 'The city will automatically appear on this state page.', 'suburban-relocation' ); ?></p>
	<?php
	$fields = array(
		'srs_kicker'       => array( __( 'Hero kicker', 'suburban-relocation' ), 'Local moving specialists' ),
		'srs_address'      => array( __( 'Service-area description', 'suburban-relocation' ), 'Serving the city and nearby communities' ),
		'srs_local_phone'  => array( __( 'Local phone', 'suburban-relocation' ), '800-816-6834' ),
		'srs_service_area' => array( __( 'Route summary', 'suburban-relocation' ), 'Local and long-distance routes' ),
	);
	foreach ( $fields as $key => $field ) : ?>
		<p><label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $field[0] ); ?></strong></label></p>
		<input class="widefat" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, '_' . $key, true ) ); ?>" placeholder="<?php echo esc_attr( $field[1] ); ?>">
	<?php endforeach;
}
add_action( 'add_meta_boxes', 'srs_add_meta_boxes' );

function srs_service_meta_box( $post ) {
	wp_nonce_field( 'srs_save_meta', 'srs_meta_nonce' );
	?>
	<p><label for="srs_kicker"><strong><?php esc_html_e( 'Hero kicker', 'suburban-relocation' ); ?></strong></label></p>
	<input class="widefat" id="srs_kicker" name="srs_kicker" value="<?php echo esc_attr( get_post_meta( $post->ID, '_srs_kicker', true ) ); ?>" placeholder="Professional moving service">
	<p class="description"><?php esc_html_e( 'Shown above the article title.', 'suburban-relocation' ); ?></p>
	<p><label for="srs_card_label"><strong><?php esc_html_e( 'Short card label', 'suburban-relocation' ); ?></strong></label></p>
	<input class="widefat" id="srs_card_label" name="srs_card_label" value="<?php echo esc_attr( get_post_meta( $post->ID, '_srs_card_label', true ) ); ?>" placeholder="Local expertise">
	<p class="description"><?php esc_html_e( 'Optional. The excerpt supplies the service-card description.', 'suburban-relocation' ); ?></p>
	<?php
}

function srs_location_meta_box( $post ) {
	wp_nonce_field( 'srs_save_meta', 'srs_meta_nonce' );
	$fields = array(
		'srs_kicker'       => array( __( 'Hero kicker', 'suburban-relocation' ), 'Local moving specialists' ),
		'srs_state'        => array( __( 'State / region', 'suburban-relocation' ), 'Maryland' ),
		'srs_address'      => array( __( 'Office or service-area address', 'suburban-relocation' ), '12000 Old Baltimore Pike, Beltsville, MD 20705' ),
		'srs_local_phone'  => array( __( 'Location phone', 'suburban-relocation' ), '800-816-6834' ),
		'srs_service_area' => array( __( 'Service area summary', 'suburban-relocation' ), 'Local and long-distance routes' ),
	);
	foreach ( $fields as $key => $field ) :
		?>
		<p><label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $field[0] ); ?></strong></label></p>
		<input class="widefat" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_post_meta( $post->ID, '_' . $key, true ) ); ?>" placeholder="<?php echo esc_attr( $field[1] ); ?>">
	<?php endforeach;
	$featured = get_post_meta( $post->ID, '_srs_featured', true );
	?>
	<p><label><input type="checkbox" name="srs_featured" value="1" <?php checked( $featured, '1' ); ?>> <strong><?php esc_html_e( 'Show in the home-page Locations section', 'suburban-relocation' ); ?></strong></label></p>
	<p class="description"><?php esc_html_e( 'The full Locations archive always includes every published location.', 'suburban-relocation' ); ?></p>
	<?php
}

function srs_lead_meta_box( $post ) {
	$labels = array(
		'_srs_full_name' => __( 'Full name', 'suburban-relocation' ),
		'_srs_phone'     => __( 'Phone number', 'suburban-relocation' ),
		'_srs_move_date' => __( 'Preferred move date', 'suburban-relocation' ),
		'_srs_email'     => __( 'Email', 'suburban-relocation' ),
		'_srs_from'      => __( 'Moving from', 'suburban-relocation' ),
		'_srs_to'        => __( 'Moving to', 'suburban-relocation' ),
		'_srs_source'    => __( 'Source page', 'suburban-relocation' ),
	);
	echo '<table class="widefat striped"><tbody>';
	foreach ( $labels as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		if ( '_srs_source' === $key && filter_var( $value, FILTER_VALIDATE_URL ) ) {
			$value = '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>';
		} else {
			$value = esc_html( $value ? $value : '—' );
		}
		echo '<tr><th style="width:180px">' . esc_html( $label ) . '</th><td>' . wp_kses_post( $value ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

function srs_save_post_meta( $post_id ) {
	if ( ! isset( $_POST['srs_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['srs_meta_nonce'] ) ), 'srs_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$allowed = array(
		'srs_service'  => array( 'srs_kicker', 'srs_card_label' ),
		'srs_location' => array( 'srs_kicker', 'srs_state', 'srs_address', 'srs_local_phone', 'srs_service_area' ),
		'srs_city'     => array( 'srs_kicker', 'srs_address', 'srs_local_phone', 'srs_service_area' ),
	);
	$post_type = get_post_type( $post_id );
	if ( empty( $allowed[ $post_type ] ) ) {
		return;
	}
	foreach ( $allowed[ $post_type ] as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, '_' . $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
	if ( 'srs_location' === $post_type ) {
		update_post_meta( $post_id, '_srs_featured', isset( $_POST['srs_featured'] ) ? '1' : '0' );
	}
	if ( 'srs_city' === $post_type && isset( $_POST['srs_parent_state'] ) ) {
		$parent_state = absint( $_POST['srs_parent_state'] );
		if ( $parent_state && 'srs_location' === get_post_type( $parent_state ) ) update_post_meta( $post_id, '_srs_parent_state', $parent_state );
	}
}
add_action( 'save_post', 'srs_save_post_meta' );

function srs_service_columns( $columns ) {
	$columns['srs_kicker'] = __( 'Kicker', 'suburban-relocation' );
	return $columns;
}
add_filter( 'manage_srs_service_posts_columns', 'srs_service_columns' );

function srs_location_columns( $columns ) {
	$columns['srs_region']  = __( 'Region', 'suburban-relocation' );
	$columns['srs_address'] = __( 'Address / service area', 'suburban-relocation' );
	$columns['srs_cities']  = __( 'Cities', 'suburban-relocation' );
	return $columns;
}
add_filter( 'manage_srs_location_posts_columns', 'srs_location_columns' );

function srs_custom_column_content( $column, $post_id ) {
	if ( 'srs_kicker' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_srs_kicker', true ) );
	}
	if ( 'srs_region' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_srs_state', true ) );
	}
	if ( 'srs_address' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_srs_address', true ) );
	}
	if ( 'srs_cities' === $column ) {
		$count = get_posts( array( 'post_type' => 'srs_city', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_srs_parent_state', 'meta_value' => $post_id ) );
		echo esc_html( count( $count ) );
	}
}
add_action( 'manage_srs_service_posts_custom_column', 'srs_custom_column_content', 10, 2 );
add_action( 'manage_srs_location_posts_custom_column', 'srs_custom_column_content', 10, 2 );

function srs_city_columns( $columns ) {
	$columns['srs_parent_state'] = __( 'State', 'suburban-relocation' );
	$columns['srs_address']      = __( 'Service area', 'suburban-relocation' );
	return $columns;
}
add_filter( 'manage_srs_city_posts_columns', 'srs_city_columns' );

function srs_city_column_content( $column, $post_id ) {
	if ( 'srs_parent_state' === $column ) {
		$state_id = absint( get_post_meta( $post_id, '_srs_parent_state', true ) );
		echo $state_id ? '<a href="' . esc_url( get_edit_post_link( $state_id ) ) . '">' . esc_html( get_the_title( $state_id ) ) . '</a>' : '—';
	}
	if ( 'srs_address' === $column ) echo esc_html( get_post_meta( $post_id, '_srs_address', true ) );
}
add_action( 'manage_srs_city_posts_custom_column', 'srs_city_column_content', 10, 2 );

function srs_city_state_admin_filter() {
	global $typenow;
	if ( 'srs_city' !== $typenow ) return;
	$selected = isset( $_GET['srs_parent_state_filter'] ) ? absint( $_GET['srs_parent_state_filter'] ) : 0;
	$states = get_posts( array( 'post_type' => 'srs_location', 'post_status' => 'any', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	echo '<select name="srs_parent_state_filter"><option value="">' . esc_html__( 'All states', 'suburban-relocation' ) . '</option>';
	foreach ( $states as $state ) echo '<option value="' . esc_attr( $state->ID ) . '" ' . selected( $selected, $state->ID, false ) . '>' . esc_html( $state->post_title ) . '</option>';
	echo '</select>';
}
add_action( 'restrict_manage_posts', 'srs_city_state_admin_filter' );

function srs_filter_cities_by_state( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'srs_city' !== $query->get( 'post_type' ) ) return;
	if ( ! empty( $_GET['srs_parent_state_filter'] ) ) {
		$query->set( 'meta_key', '_srs_parent_state' );
		$query->set( 'meta_value', absint( $_GET['srs_parent_state_filter'] ) );
	}
}
add_action( 'pre_get_posts', 'srs_filter_cities_by_state' );
