<?php
/**
 * Migration class for the Above Fold Tracker plugin
 *
 * @package     AboveFoldTracker
 * @since       1.0.0
 * @author      Samson Ogheneakporbo Moses
 * @license     GPL-2.0-or-later
 */

namespace Above_Fold_Tracker\Services;

if (class_exists('AFT_Database')) {
	return;
}

class AFT_Database
{
	/**
	 * Create necessary database tables for the plugin.
	 *
	 * @return void
	 */
	public static function create_tables()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'above_fold_tracker';
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
		dbDelta($sql);
	}

	/**
	 * Drop the database table.
	 */
	public static function drop_table()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'above_fold_tracker';
		$wpdb->query("DROP TABLE IF EXISTS {$table_name}");
	}
}