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
			"SELECT DISTINCT visit_id, screen_width, screen_height, visit_time, page_url
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
                echo '<p><strong>Page URL:</strong> <a href="' . esc_url( $visit->page_url ) . '" target="_blank">' . esc_html( $visit->page_url ) . '</a></p>';

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

    /**
     * Displays the settings page.
     *
     * @return void
     */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Above Fold Tracker Settings', 'aft' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'aft_settings_group' );
				do_settings_sections( 'aft-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Displays the documentation page.
	 *
	 * @return void
	 */
	public function render_documentation_page() {
		?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Above Fold Tracker - Documentation', 'aft' ); ?></h1>
            <p><?php esc_html_e( 'The Above Fold Tracker plugin helps you track which hyperlinks users see above the fold when they visit your homepage or other pages.', 'aft' ); ?></p>

            <h2><?php esc_html_e( 'Features', 'aft' ); ?></h2>
            <ul>
                <li><?php esc_html_e( 'Track above the fold links on the homepage or on all pages (based on settings).', 'aft' ); ?></li>
                <li><?php esc_html_e( 'Limit tracking frequency per user using a rate limit to prevent spam.', 'aft' ); ?></li>
                <li><?php esc_html_e( 'View top tracked links in the Reports section.', 'aft' ); ?></li>
                <li><?php esc_html_e( 'View individual visit sessions and their tracked links in the Visits section.', 'aft' ); ?></li>
                <li><?php esc_html_e( 'Fully GDPR-compliant as no personal data is stored.', 'aft' ); ?></li>
            </ul>

            <h2><?php esc_html_e('Settings', 'aft'); ?></h2>
            <ul>
                <li><strong><?php esc_html_e('Track on All Pages:', 'aft'); ?></strong> <?php esc_html_e('If enabled, the plugin will track links on all pages instead of just the homepage.', 'aft'); ?></li>
                <li><strong><?php esc_html_e('Rate Limit (Seconds):', 'aft'); ?></strong> <?php esc_html_e('Set the minimum number of seconds between allowed tracking requests from the same visitor.', 'aft'); ?></li>
                <li><strong><?php esc_html_e('Data Retention (Days):', 'aft'); ?></strong> <?php esc_html_e('Specify how many days to retain tracking data before it is automatically deleted. Default is 7 days.', 'aft'); ?></li>
            </ul>

            <h2><?php esc_html_e( 'How it Works', 'aft' ); ?></h2>
            <p><?php esc_html_e( 'The plugin injects a lightweight JavaScript on the frontend that detects which hyperlinks are visible above the fold when a visitor loads the page. These links, along with screen size and visit timestamp, are sent via AJAX to the WordPress backend and stored in the database. You can then analyze this data using the Reports and Visits pages in the admin dashboard.', 'aft' ); ?></p>

            <h2><?php esc_html_e( 'Support', 'aft' ); ?></h2>
            <p><?php esc_html_e( 'If you encounter issues or have suggestions, please contact the plugin developer.', 'aft' ); ?></p>
        </div>
		<?php
	}

}
