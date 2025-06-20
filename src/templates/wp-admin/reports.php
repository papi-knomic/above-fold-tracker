<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
} ?>

<div class="wrap">
	<h1><?php esc_html_e( 'Above Fold Tracker - Top Links', 'aft' ); ?></h1>

	<table class="widefat fixed striped">
		<thead>
		<tr>
			<th><?php esc_html_e( 'Link', 'aft' ); ?></th>
			<th><?php esc_html_e( 'Views', 'aft' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if ( $results ) : ?>
			<?php foreach ( $results as $row ) : ?>
				<tr>
					<td><a href="<?php echo esc_url( $row->url ); ?>" target="_blank"><?php echo esc_html( $row->url ); ?></a></td>
					<td><?php echo intval( $row->total ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="2"><?php esc_html_e( 'No reports available yet.', 'aft' ); ?></td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
</div>
