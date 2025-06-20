<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
} ?>

<input type="number" name="aft_data_retention_days" value="<?php echo esc_attr( $value ); ?>" min="1">
<label><?php esc_html_e( 'Number of days to keep tracking data before automatic cleanup.', 'aft' ); ?></label>