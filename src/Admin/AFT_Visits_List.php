<?php

namespace AFT\Plugin\Admin;

class AFT_Visits_List extends \WP_List_Table {

	/**
	 * Sets up some table attributes (i.e: the plurals and whether it's ajax or not)
	 */
	public function __construct() {

		// Set parent defaults
		parent::__construct(
			array(
				'singular' => 'item', // singular name of the listed records
				'plural'   => 'items',  // plural name of the listed records
				'ajax'     => false,       // does this table support ajax?
			)
		);
	}

	public function get_columns() {
		return array(
			'visit_id'    => __( 'Visit ID', 'aft' ),
			'screen_size' => __( 'Screen Size', 'aft' ),
			'visit_time'  => __( 'Visit Time', 'aft' ),
			'page_url'    => __( 'Page URL', 'aft' ),
			'links'       => __( 'View Links', 'aft' ),
		);
	}

	/**
	 * Returns the default column item
	 *
	 * @param object $item
	 * @param string $column_name
	 * @return void
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}


	public function column_screen_size( $item ) {
		return sprintf( '%d x %d', $item->screen_width, $item->screen_height );
	}

	public function column_visit_time( $item ) {
		return date( 'Y-m-d H:i:s', $item->visit_time );
	}

	public function column_page_url( $item ) {
		return '<a href="' . esc_url( $item->page_url ) . '" target="_blank">' . esc_html( $item->page_url ) . '</a>';
	}

	public function column_links( $item ) {
		$view_url = admin_url( 'admin.php?page=aft_visits&view=' . $item->visit_id );
		return sprintf(
			'<a href="%s" class="button button-primary">%s</a>',
			esc_url( $view_url ),
			__( 'View Details', 'aft' )
		);
	}


	/**
	 * Prepare table items with pagination, search, and filters.
	 */
	public function prepare_items() {
		global $wpdb;

		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;
		$table_name   = $wpdb->prefix . 'above_fold_tracker';

		$total_items = $wpdb->get_var( "SELECT COUNT(DISTINCT visit_id) FROM {$table_name}" );

		$this->items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT visit_id, screen_width, screen_height, visit_time, page_url 
                 FROM {$table_name} 
                 ORDER BY visit_time DESC 
                 LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
	}
}
