<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
} ?>

<div class="wrap">
    <h1><?php esc_html_e( 'Above the Fold Tracker - Visit Sessions', 'aft' ); ?></h1>

	<?php
	// Prepare the table items
	$aft_visits_list->prepare_items();
	?>

    <form id="tables-filter" method="get">
        <input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? esc_attr( $_REQUEST['page'] ) : ''; ?>" />
		<?php
		// Display the table
		$aft_visits_list->display();
		?>
    </form>
</div>
