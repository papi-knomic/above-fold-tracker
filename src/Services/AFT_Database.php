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
            url VARCHAR(255) NOT NULL,
            screen_width INT NOT NULL,
            screen_height INT NOT NULL,
            visit_time INT NOT NULL, -- Unix timestamp
            visit_id VARCHAR(64) NOT NULL,
            page_url VARCHAR(255) NOT NULL,
            KEY visit_id (visit_id),
            KEY url (url),
            KEY visit_time (visit_time)
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
	 * @param array  $links Array of links to track.
	 * @param array  $screen_size Array containing 'width' and 'height' of the screen.
	 * @param string $visit_id Unique identifier for the visit.
	 * @param string $page_url URL of the page being tracked.
	 *
	 * @return void
	 */
	public static function store_tracking_data( $links, $screen_size, $visit_id, $page_url ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'above_fold_tracker';
		$rate_limit = get_option( 'aft_rate_limit_seconds', 10 ); // Default to 10 seconds if not set.
		$rate_limit = intval( $rate_limit );
		$rate_limit = $rate_limit > 0 ? $rate_limit : 10; // Ensure a positive rate limit.

		$recent_entry = $wpdb->get_var(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- This is a simple query that does not require caching.
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $table_name WHERE visit_id = %s AND visit_time > %d",  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- the table name can not be passed in the prepare statement.
				$visit_id,
				( time() - $rate_limit )
			)
		);

		if ( $recent_entry > 0 ) {
			return;
		}

		$screen_width  = intval( $screen_size['width'] );
		$screen_height = intval( $screen_size['height'] );

		$visit_time = time();
		$page_url   = esc_url_raw( $page_url );

		foreach ( $links as $link ) {

			if ( ! filter_var( $link, FILTER_VALIDATE_URL ) ) {
				continue;
			}

			$prepared_link = esc_url_raw( $link );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery -- Cache is not needed for this operation.
			$wpdb->insert(
				$table_name,
				array(
					'url'           => $prepared_link,
					'screen_width'  => $screen_width,
					'screen_height' => $screen_height,
					'visit_time'    => $visit_time,
					'visit_id'      => $visit_id,
					'page_url'      => $page_url,
				),
				array(
					'%s',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
				)
			);
		}
	}

	/**
	 * Cleans up tracking records older than the retention period.
	 *
	 * @return void
	 */
	public static function cleanup_old_tracking_data() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'above_fold_tracker';

		// Get the retention period from settings (default to 7 days if not set).
		$retention_days = intval( get_option( 'aft_data_retention_days', 7 ) );

		// Ensure retention days is a positive integer.
		if ( $retention_days <= 0 ) {
			$retention_days = 7;
		}

		$cutoff_time = time() - ( $retention_days * DAY_IN_SECONDS );

		$wpdb->query(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- This is a simple query that does not require caching.
			$wpdb->prepare(
				"DELETE FROM {$table_name} WHERE visit_time < %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- the table name can not be passed in the prepare statement.
				$cutoff_time
			)
		);
	}
}
