<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
} ?>

<input type="number" name="aft_rate_limit_seconds" value="<?php echo esc_attr( $value ); ?>" min="1">
<label><?php esc_html_e( 'Minimum seconds between allowed tracking requests per visitor.', 'aft' ); ?></label>