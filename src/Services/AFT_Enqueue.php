<?php






namespace AFT\Plugin\Services;

class AFT_Enqueue {

	/**
	 * Initializes the enqueue service by setting up hooks for scripts and styles.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

		if ( $this->is_aft_admin_page() ) {
			add_action( 'admin_print_scripts', array( $this, 'admin_menu_page_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'admin_menu_page_styles' ) );
		}
	}

	/**
	 * Enqueues frontend scripts and styles.
	 *
	 * @return void
	 */
	public function enqueue_frontend_scripts() {
		$load_on_all_pages = get_option( 'aft_show_on_all_pages', '0' );
		$load_on_all_pages = '1' === $load_on_all_pages;

		if ( ! $load_on_all_pages && ! is_front_page() ) {
			return;
		}

		wp_enqueue_script(
			'above-fold-tracker-frontend',
			plugins_url( '../js/aft_frontend.js', __FILE__ ),
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_localize_script(
			'above-fold-tracker-frontend',
			'aft_frontend_data',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'af_tracker_nonce' ),
			)
		);
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @return void
	 */
	public function admin_menu_page_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'thickbox' );
	}

	/**
	 * Enqueues admin styles.
	 *
	 * @return void
	 */
	public function admin_menu_page_styles() {
		wp_enqueue_style( 'thickbox' );
	}

	/**
	 * Checks if the current page is an AFT admin page.
	 *
	 * @return bool True if it's an AFT admin page, false otherwise.
	 */
	private function is_aft_admin_page() {
		global $pagenow;

		return 'admin.php' === $pagenow && isset( $_GET['page'] ) && strpos( $_GET['page'], 'aft' ) !== false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- No nonce on _GET.
	}
}
