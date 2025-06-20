<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );} ?>



<div class="wrap">
	<h1>Above the Fold Tracker - Visit Sessions</h1>';
	<div class="postbox">
		<div class="inside">
			<?php
			$transactionList->prepare_items();
			?>
			<form id="tables-filter" method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
				<?php
				$transactionList->display();
				?>
			</form>
		</div>
	</div>
</div>';
