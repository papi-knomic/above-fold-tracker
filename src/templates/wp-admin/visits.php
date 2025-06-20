<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
} ?>

<div class="wrap">
	<h1><?php esc_html_e( 'Above the Fold Tracker - Visit Sessions', 'aft' ); ?></h1>

	<?php
		$aft_visits_list->prepare_items();
	?>

	<form id="tables-filter" method="get">
		<?php
			$aft_visits_list->display();
		?>
	</form>
</div>
