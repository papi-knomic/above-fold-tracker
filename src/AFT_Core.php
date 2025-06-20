<?php
/**
 * Plugin core class
 *
 * @package     AboveFoldTracker
 * @since       1.0.0
 * @author      Samson Ogheneakporbo Moses
 * @license     GPL-2.0-or-later
 */

namespace AFT\Plugin;

use AFT\Plugin\Admin\AFT_Admin;
use AFT\Plugin\Services\AFT_Database;
use AFT\Plugin\Services\AFT_Settings;
use AFT\Plugin\Services\AFT_Tracker;

if ( class_exists( 'AFT_Core' ) ) {
	return;
}

/**
 * Main plugin class. It manages initialization, install, and activations.
 */
class AFT_Core {

	public $plugin_url;

	public $plugin_path;

	/**
	 * Initializes the plugin by loading necessary files and setting up hooks.
	 *
	 * @return void
	 */
	public function init() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			require_once plugin_dir_path( __DIR__ ) . 'src/Support/exceptions.php';
		}

		( new AFT_Admin() )->init();
		( new AFT_Settings() )->init();
		( new AFT_Tracker() )->init();

		add_action( 'aft_cleanup_event', array( '\AFT\Plugin\Services\AFT_Database', 'cleanup_old_tracking_data' ) );
	}

	/**
	 * Loads a PHP template file and passes variables to it.
	 *
	 * @param string $template The template filename relative to src/templates (without .php extension).
	 * @param array  $variables Associative array of variables to extract for the template.
	 *
	 * @return void
	 */
	public static function load_template( string $template, array $variables = array() ) {
		$plugin_path   = untrailingslashit( plugin_dir_path( __FILE__ ) );
		$template_path = $plugin_path . '/templates/' . $template;

		if ( ! file_exists( $template_path ) ) {
			wp_die( esc_html__( 'Template not found: ', 'aft' ) . esc_html( $template_path ) );
		}

		// Extract variables to local scope
		extract( $variables );

		// Load the template
		include $template_path;
	}


	/**
	 * Handles plugin activation.
	 *
	 * @return void
	 */
	public static function af_tracker_activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "activate-plugin_{$plugin}" );

		AFT_Database::create_tables();

		if ( ! wp_next_scheduled( 'above_fold_tracker_cleanup' ) ) {
			wp_schedule_event( time(), 'daily', 'above_fold_tracker_cleanup' );
		}
	}

	/**
	 * Handles plugin deactivation.
	 *
	 * @return void
	 */
	public static function af_tracker_deactivate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin = isset( $_REQUEST['plugin'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ) : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );

		wp_clear_scheduled_hook( 'above_fold_tracker_cleanup' );
	}

	/**
	 * Handles plugin uninstall.
	 *
	 * @return void
	 */
	public static function af_tracker_uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		AFT_Database::drop_table();
	}
}
