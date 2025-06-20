<?php

namespace AFT\Plugin\Services;

use AFT\Plugin\AFT_Core;

class AFT_Settings {

	/**
	 * Initializes the settings service.
	 *
	 * This method hooks into the 'admin_init' action to register settings.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Registers the settings for the Above Fold Tracker plugin.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'aft_settings_group', 'aft_show_on_all_pages' );
		register_setting( 'aft_settings_group', 'aft_rate_limit_seconds' );
		register_setting( 'aft_settings_group', 'aft_data_retention_days' );

		add_settings_section(
			'aft_main_settings',
			__( 'Above Fold Tracker Settings', 'aft' ),
			null,
			'aft-settings'
		);

		add_settings_field(
			'aft_show_on_all_pages',
			__( 'Track on All Pages', 'aft' ),
			array( $this, 'show_on_all_pages_field' ),
			'aft-settings',
			'aft_main_settings'
		);

		add_settings_field(
			'aft_rate_limit_seconds',
			__( 'Rate Limit (Seconds)', 'aft' ),
			array( $this, 'rate_limit_seconds_field' ),
			'aft-settings',
			'aft_main_settings'
		);

		add_settings_field(
			'aft_data_retention_days',
			__( 'Data Retention (Days)', 'aft' ),
			array( $this, 'data_retention_days_field' ),
			'aft-settings',
			'aft_main_settings'
		);
	}

	/**
	 * Field for showing the option to track above the fold on all pages.
	 *
	 * @return void
	 */
	public function show_on_all_pages_field() {
		$value = get_option( 'aft_show_on_all_pages', '0' );

		AFT_Core::load_template(
			'wp-admin/partials/all-pages.php',
			array(
				'value' => $value,
			)
		);
	}

	/**
	 * Field for setting the rate limit in seconds.
	 *
	 * @return void
	 */
	public function rate_limit_seconds_field() {
		$value = get_option( 'aft_rate_limit_seconds', 10 );

		AFT_Core::load_template(
			'wp-admin/partials/rate-limit.php',
			array(
				'value' => $value,
			)
		);
	}

	/**
	 * Field for setting the number of days to retain tracking data.
	 *
	 * @return void
	 */
	public function data_retention_days_field() {
		$value = get_option( 'aft_data_retention_days', 7 );
		AFT_Core::load_template(
			'wp-admin/partials/data-retention.php',
			array(
				'value' => $value,
			)
		);
	}
}
