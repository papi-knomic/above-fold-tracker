<?php
/**
 * Plugin Name: Above The Fold Link Tracker
 * Description: Tracks hyperlinks visible above the fold on the homepage and displays recent views.
 * Version: 1.0.0
 * Author: Samson Ogheneakporbo Moses
 * License: GPL-2.0+
 * Requires PHP: 7.3
 * Requires at least: 6.0
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

use Above_Fold_Tracker_Plugin\AFT_Core;

if ( class_exists('Above_Fold_Tracker_Plugin\AFT_Core') ) {
	$aft_core = new AFT_Core();
	$aft_core->init();

	register_activation_hook( __FILE__, array('Above_Fold_Tracker_Plugin\AFT_Core', 'af_tracker_activate' ) );
	register_deactivation_hook( __FILE__, array('Above_Fold_Tracker_Plugin\AFT_Core', 'af_tracker_deactivate' ) );
	register_uninstall_hook( __FILE__, array('Above_Fold_Tracker_Plugin\AFT_Core', 'af_tracker_uninstall' ) );
}
