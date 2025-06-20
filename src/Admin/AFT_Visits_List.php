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
			'links'       => __( 'Links', 'aft' ),
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
		return $item->{$column_name};
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
		if ( empty( $item->links ) ) {
			return 'No links tracked';
		}

		$details = implode( "\n", array_map( 'esc_url', $item->links ) );

		$output  = sprintf(
			'<a href="#TB_inline?width=600&height=400&inlineId=links-%s" class="thickbox button">View Links</a>',
			esc_attr( $item->visit_id )
		);

		$output .= sprintf(
			'<div id="links-%s" style="display:none;"><pre>%s</pre></div>',
			esc_attr( $item->visit_id ),
			esc_html( $details )
		);

		return $output;
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

		$visits = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT visit_id, screen_width, screen_height, visit_time, page_url 
             FROM {$table_name} 
             ORDER BY visit_time DESC 
             LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

		// For each visit, fetch links and store them
		foreach ( $visits as &$visit ) {
			$visit->links = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT url FROM {$table_name} WHERE visit_id = %s",
					$visit->visit_id
				)
			);
		}

		$this->items = $visits;

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			array(),
		);

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}
}
