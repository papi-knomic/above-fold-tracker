<?php
/**
 * Migration class for the Above Fold Tracker plugin
 *
 * @package     AboveFoldTracker
 * @since       1.0.0
 * @author      Samson Ogheneakporbo Moses
 * @license     GPL-2.0-or-later
 */

namespace AFT\Plugin\Services;

if ( class_exists( 'AFT_Database' ) ) {
	return;
}

class AFT_Database {

	/**
	 * Create necessary database tables for the plugin.
	 *
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'above_fold_tracker';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            url TEXT NOT NULL,
            screen_width INT NOT NULL,
            screen_height INT NOT NULL,
            visit_time INT NOT NULL, -- Unix timestamp
            visit_id VARCHAR(64) NOT NULL,
            KEY visit_time_index (visit_time)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Drop the database table.
	 */
	public static function drop_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'above_fold_tracker';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Schema changes are safe
		$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
	}

	/**
	 * Store tracking data in the database.
	 *
	 * @param array $links Array of links to track.
	 * @param array $screen_size Array containing 'width' and 'height' of the screen.
	 */
	public static function store_tracking_data( array $links, array $screen_size ) {
		global $wpdb;

		$table_name    = $wpdb->prefix . 'above_fold_tracker';
		$screen_width  = intval( $screen_size['width'] );
		$screen_height = intval( $screen_size['height'] );

		$visit_id   = md5( $_SERVER['REMOTE_ADDR'] . wp_rand() );
		$visit_time = time();

		foreach ( $links as $link ) {
			$prepared_link = esc_url_raw( $link );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching -- Tracking writes are acceptable here
			$wpdb->insert(
				$table_name,
				array(
					'url'           => $prepared_link,
					'screen_width'  => $screen_width,
					'screen_height' => $screen_height,
					'visit_time'    => $visit_time,
					'visit_id'      => $visit_id,
				),
				array(
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
				)
			);
		}
	}
}
