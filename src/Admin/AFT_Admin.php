<?php
/**
 * Plugin admin service class.
 *
 * @package     AboveFoldTracker
 * @since       1.0.0
 * @author      Samson Ogheneakporbo Moses
 * @license     GPL-2.0-or-later
 */

namespace AFT\Plugin\Admin;

use AFT\Plugin\AFT_Core;

if ( class_exists( 'AFT_Admin' ) ) {
	return;
}

class AFT_Admin {

	/**
	 * Initializes the admin service.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script('thickbox');
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
			__( 'Reports', 'aft' ),
			'manage_options',
			'aft',
			array( $this, 'render_reports_page' )
		);

		add_submenu_page(
			'aft',
			__( 'Visits', 'aft' ),
			__( 'Visits', 'aft' ),
			'manage_options',
			'aft-visits',
			array( $this, 'render_visits_page' )
		);

		add_submenu_page(
			'aft',
			__( 'Settings', 'aft' ),
			__( 'Settings', 'aft' ),
			'manage_options',
			'aft-settings',
			array( $this, 'render_settings_page' )
		);

		add_submenu_page(
			'aft',
			__( 'Documentation', 'aft' ),
			__( 'Documentation', 'aft' ),
			'manage_options',
			'aft-documentation',
			array( $this, 'render_documentation_page' )
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

		AFT_Core::load_template(
			'wp-admin/reports.php',
			array(
				'results' => $results,
			)
		);
	}

	/**
	 * Displays the visits page.
	 *
	 * @return void
	 */
	public function render_visits_page() {
		$aft_visits_list = new AFT_Visits_List();

		AFT_Core::load_template(
			'wp-admin/visits.php',
			array(
				'aft_visits_list' => $aft_visits_list,
			)
		);
	}

	/**
	 * Displays the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		AFT_Core::load_template( 'wp-admin/settings.php' );
		// You can add more settings-related code here if needed.
		// For example, handling form submissions or displaying settings fields.
	}

	/**
	 * Displays the documentation page.
	 *
	 * @return void
	 */
	public function render_documentation_page() {
		AFT_Core::load_template( 'wp-admin/documentation.php' );
	}
}
