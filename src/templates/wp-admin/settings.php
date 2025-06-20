<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
} ?>

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



