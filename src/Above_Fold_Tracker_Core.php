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

use Above_Fold_Tracker\Services\AFT_Database;

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
	public function init() {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			require_once plugin_dir_path(__DIR__) . '/Support/exceptions.php';
		}

		add_action('wp_enqueue_scripts', [$this, 'enqueue_tracking_script']);
	}

	/**
	 * Enqueues the tracking script on the frontend.
	 *
	 * @return void
	 */
	public function enqueue_tracking_script() {
		if (!is_front_page()) {
			return; // Load script only on the homepage
		}

		wp_enqueue_script(
			'above-fold-tracker-script',
			plugins_url('assets/js/tracker.js', __FILE__),
			[],
			'1.0.0',
			true
		);

		wp_localize_script(
			'above-fold-tracker-script',
			'aft_data',
			[
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'    => wp_create_nonce('af_tracker_nonce'),
			]
		);
	}

	/**
	 * Handles plugin activation.
	 *
	 * @return void
	 */
	public static function af_tracker_activate() {
		if (!current_user_can('activate_plugins')) {
			return;
		}

		$plugin = isset($_REQUEST['plugin']) ? sanitize_text_field(wp_unslash($_REQUEST['plugin'])) : '';
		check_admin_referer("activate-plugin_{$plugin}");

		AFT_Database::create_tables();

		if (!wp_next_scheduled('above_fold_tracker_cleanup')) {
			wp_schedule_event(time(), 'daily', 'above_fold_tracker_cleanup');
		}
	}

	/**
	 * Handles plugin deactivation.
	 *
	 * @return void
	 */
	public static function af_tracker_deactivate() {
		if (!current_user_can('activate_plugins')) {
			return;
		}

		$plugin = isset($_REQUEST['plugin']) ? sanitize_text_field(wp_unslash($_REQUEST['plugin'])) : '';
		check_admin_referer("deactivate-plugin_{$plugin}");

		wp_clear_scheduled_hook('above_fold_tracker_cleanup');
	}

	/**
	 * Handles plugin uninstall.
	 *
	 * @return void
	 */
	public static function af_tracker_uninstall() {
		if (!current_user_can('activate_plugins')) {
			return;
		}

		global $wpdb;

		$table_name = $wpdb->prefix . 'above_fold_tracking';
		$wpdb->query("DROP TABLE IF EXISTS {$table_name}");
	}
}
