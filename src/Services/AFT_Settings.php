<?php

namespace AFT\Plugin\Services;

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
		?>
		<input type="checkbox" name="aft_show_on_all_pages" value="1" <?php checked( 1, $value ); ?>>
		<label><?php esc_html_e( 'Track above the fold on all pages instead of only the front page.', 'aft' ); ?></label>
		<?php
	}

	/**
	 * Field for setting the rate limit in seconds.
	 *
	 * @return void
	 */
	public function rate_limit_seconds_field() {
		$value = get_option( 'aft_rate_limit_seconds', 10 );
		?>
		<input type="number" name="aft_rate_limit_seconds" value="<?php echo esc_attr( $value ); ?>" min="1">
		<label><?php esc_html_e( 'Minimum seconds between allowed tracking requests per visitor.', 'aft' ); ?></label>
		<?php
	}

	/**
	 * Field for setting the number of days to retain tracking data.
	 *
	 * @return void
	 */
	public function data_retention_days_field() {
		$value = get_option( 'aft_data_retention_days', 7 );
		?>
		<input type="number" name="aft_data_retention_days" value="<?php echo esc_attr( $value ); ?>" min="1">
		<label><?php esc_html_e( 'Number of days to keep tracking data before automatic cleanup.', 'aft' ); ?></label>
		<?php
	}
}
