<?php
/**
 * Plugin core class
 *
 * @package     AboveFoldTracker
 * @since       1.0.0
 * @author      Samson Ogheneakporbo Moses
 * @license     GPL-2.0-or-later
 */

namespace Above_Fold_Tracker;

if (class_exists('Above_Fold_Tracker_Core')) {
	return;
}

/**
 * Main plugin class. It manages initialization, install, and activations.
 */
class Above_Fold_Tracker_Core {

	/**
	 * Initializes the plugin by loading necessary files and setting up hooks.
	 *
	 * @return void
	 */
	public static function init()
	{
		if (defined('WP_DEBUG') && WP_DEBUG) {
			require_once plugin_dir_path(__FILE__) . 'support/exceptions.php';
		}
	}

	/**
	 * Handles plugin activation:
	 *
	 * @return void
	 */
	public static function af_tracker_activate() {
		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "activate-plugin_{$plugin}" );

		// Create necessary database tables
		AFT_Migration::create_tables();

		// Schedule the cleanup event
		if (!wp_next_scheduled('above_fold_tracker_cleanup')) {
			wp_schedule_event(time(), 'daily', 'above_fold_tracker_cleanup');
		}
	}

	/**
	 * Handles plugin deactivation
	 *
	 * @return void
	 */
	public function af_tracker_deactivate() {
		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );

		wp_clear_scheduled_hook('above_fold_tracker_cleanup');
	}

	/**
	 * Handles plugin uninstall
	 *
	 * @return void
	 */
	public static function af_tracker_uninstall() {

		// Security checks.
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		global $wpdb;

		// Optionally: Remove the tracking table
		$table_name = $wpdb->prefix . 'above_fold_tracking';
		$wpdb->query("DROP TABLE IF EXISTS {$table_name}");
	}
}
