<?php

namespace AFT\Plugin\Services;

if ( class_exists( 'AFT_Tracker' ) ) {
	return;
}

class AFT_Tracker {

	/**
	 * Initializes the tracker service.
	 */
	public function init() {
		add_action( 'wp_ajax_nopriv_aft_track_links', array( $this, 'handle_link_tracking' ) );
		add_action( 'wp_ajax_aft_track_links', array( $this, 'handle_link_tracking' ) );
	}

	/**
	 * Handles the AJAX request for tracking links.
	 *
	 * @return void
	 */
	public function handle_link_tracking() {
		check_ajax_referer( 'af_tracker_nonce', 'nonce' );

		if ( empty( $_POST['links'] ) || empty( $_POST['screen_size'] ) ) {
			wp_send_json_error( 'Invalid data.' );
		}

		$links = array_map( 'esc_url_raw', wp_unslash( $_POST['links'] ) );

		$screen_size = array(
			'width'  => isset( $_POST['screen_size']['width'] ) ? intval( $_POST['screen_size']['width'] ) : 0,
			'height' => isset( $_POST['screen_size']['height'] ) ? intval( $_POST['screen_size']['height'] ) : 0,
		);

		$visit_id = isset( $_COOKIE['aft_visit_id'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['aft_visit_id'] ) ) : '';

		if ( empty( $visit_id ) ) {
			$remote_addr = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Server IP address cannot be sanitized, used for unique ID only.
			$visit_id    = md5( $remote_addr . wp_rand() );
			setcookie( 'aft_visit_id', $visit_id, time() + 3600, COOKIEPATH, COOKIE_DOMAIN );
		}

		$page_url = isset( $_POST['page_url'] ) ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : '';

		AFT_Database::store_tracking_data( $links, $screen_size, $visit_id, $page_url );

		wp_send_json_success( 'Tracking data stored.' );
	}
}
