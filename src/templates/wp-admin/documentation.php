<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );} ?>

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

	<h2><?php esc_html_e( 'Settings', 'aft' ); ?></h2>
	<ul>
		<li><strong><?php esc_html_e( 'Track on All Pages:', 'aft' ); ?></strong> <?php esc_html_e( 'If enabled, the plugin will track links on all pages instead of just the homepage.', 'aft' ); ?></li>
		<li><strong><?php esc_html_e( 'Rate Limit (Seconds):', 'aft' ); ?></strong> <?php esc_html_e( 'Set the minimum number of seconds between allowed tracking requests from the same visitor.', 'aft' ); ?></li>
		<li><strong><?php esc_html_e( 'Data Retention (Days):', 'aft' ); ?></strong> <?php esc_html_e( 'Specify how many days to retain tracking data before it is automatically deleted. Default is 7 days.', 'aft' ); ?></li>
	</ul>

	<h2><?php esc_html_e( 'How it Works', 'aft' ); ?></h2>
	<p><?php esc_html_e( 'The plugin injects a lightweight JavaScript on the frontend that detects which hyperlinks are visible above the fold when a visitor loads the page. These links, along with screen size and visit timestamp, are sent via AJAX to the WordPress backend and stored in the database. You can then analyze this data using the Reports and Visits pages in the admin dashboard.', 'aft' ); ?></p>

	<h2><?php esc_html_e( 'Support', 'aft' ); ?></h2>
	<p><?php esc_html_e( 'If you encounter issues or have suggestions, please contact the plugin developer.', 'aft' ); ?></p>
</div>
