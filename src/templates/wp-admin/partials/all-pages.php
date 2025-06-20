<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
} ?>

<input type="checkbox" name="aft_show_on_all_pages" value="1" <?php checked( 1, $value ); ?>>
<label><?php esc_html_e( 'Track above the fold on all pages instead of only the front page.', 'aft' ); ?></label>
