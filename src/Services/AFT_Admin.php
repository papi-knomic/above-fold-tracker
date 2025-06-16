<?php

namespace AFT\Plugin\Services;

class AFT_Admin {

	/**
	 * Initializes the admin service.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Adds the admin menu for the plugin.
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Above Fold Tracker', 'aft' ),
			__( 'Above Fold Tracker', 'aft' ),
			'manage_options',
			'aft',
			array( $this, 'render_reports_page' ),
			'dashicons-chart-bar'
		);

		add_submenu_page(
			'aft',
			__( 'Reports', 'aft' ),
			__( 'View Reports', 'aft' ),
			'manage_options',
			'aft',
			array( $this, 'render_reports_page' )
		);

		add_submenu_page(
			'aft',
			__( 'Visits', 'aft' ),
			__( 'View Visits', 'aft' ),
			'manage_options',
			'aft-visits',
			array( $this, 'render_visits_page' )
		);
	}

	/**
	 * Displays the reports page.
	 *
	 * @return void
	 */
	public function render_reports_page() {
		global $wpdb;

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'aft' ) );
		}

		$table_name = $wpdb->prefix . 'above_fold_tracker';

		$results = $wpdb->get_results(
			"SELECT url, COUNT(*) as total FROM {$table_name} GROUP BY url ORDER BY total DESC LIMIT 10"
		);

		echo '<div class="wrap"><h1>' . esc_html__( 'Above Fold Tracker - Top Links', 'aft' ) . '</h1>';
		echo '<table class="widefat fixed striped"><thead><tr><th>Link</th><th>Visits</th></tr></thead><tbody>';

		if ( $results ) {
			foreach ( $results as $row ) {
				echo '<tr><td>' . esc_url( $row->url ) . '</td><td>' . intval( $row->total ) . '</td></tr>';
			}
		} else {
			echo '<tr><td colspan="2">' . esc_html__( 'No reports available yet.', 'aft' ) . '</td></tr>';
		}

		echo '</tbody></table></div>';
	}

	/**
	 * Displays the visits page.
	 *
	 * @return void
	 */
	public function render_visits_page() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'above_fold_tracker';

		// Get distinct visits
		$visits = $wpdb->get_results(
			"SELECT DISTINCT visit_id, screen_width, screen_height, visit_time 
         FROM {$table_name} 
         ORDER BY visit_time DESC 
         LIMIT 20"
		);

		echo '<div class="wrap"><h1>Above the Fold Tracker - Visit Sessions</h1>';

		if ( $visits ) {
			foreach ( $visits as $visit ) {
				echo '<h2>Visit ID: ' . esc_html( $visit->visit_id ) . '</h2>';
				echo '<p><strong>Screen Size:</strong> ' . intval( $visit->screen_width ) . ' x ' . intval( $visit->screen_height ) . '</p>';
				echo '<p><strong>Visit Time:</strong> ' . date( 'Y-m-d H:i:s', $visit->visit_time ) . '</p>';

				$links = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT url FROM {$table_name} WHERE visit_id = %s",
						$visit->visit_id
					)
				);

				if ( $links ) {
					echo '<ul>';
					foreach ( $links as $link ) {
						echo '<li><a href="' . esc_url( $link ) . '" target="_blank">' . esc_html( $link ) . '</a></li>';
					}
					echo '</ul>';
				} else {
					echo '<p>No links tracked for this visit.</p>';
				}

				echo '<hr>';
			}
		} else {
			echo '<p>No visits tracked yet.</p>';
		}

		echo '</div>';
	}
}
