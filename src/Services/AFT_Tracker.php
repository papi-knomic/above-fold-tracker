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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_tracking_script' ) );
		add_action( 'wp_ajax_nopriv_aft_track_links', array( $this, 'handle_link_tracking' ) );
		add_action( 'wp_ajax_aft_track_links', array( $this, 'handle_link_tracking' ) );
	}


	/**
	 * Enqueues the tracking script on the frontend.
	 *
	 * @return void
	 */
	public function enqueue_tracking_script() {
		if ( ! is_front_page() ) {
			return;
		}

		wp_enqueue_script(
			'above-fold-tracker-frontend',
			plugins_url( '../js/aft_frontend.js', __FILE__ ),
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_localize_script(
			'above-fold-tracker-frontend',
			'aft_frontend_data',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'af_tracker_nonce' ),
			)
		);
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

		$links       = array_map( 'esc_url_raw', $_POST['links'] );
		$screen_size = array(
			'width'  => isset($_POST['screen_size']['width']) ? intval( $_POST['screen_size']['width'] ) : 0,
			'height' => isset($_POST['screen_size']['height']) ? intval( $_POST['screen_size']['height'] ) : 0,
		);

		AFT_Database::store_tracking_data( $links, $screen_size );

		wp_send_json_success( 'Tracking data stored.' );
	}
}
