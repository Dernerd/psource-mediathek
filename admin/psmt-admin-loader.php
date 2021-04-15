<?php
/**
 * Admin loader
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load our admin panel, files for admin features.
 */
function psmt_admin_load() {

	if ( ! is_admin() || ( is_admin() && defined( 'DOING_AJAX' ) ) ) {
		return;
	}

	$path = psourcemediathek()->get_path() . 'admin/';

	$files = array(
		'psmt-admin-functions.php',

		'psmt-admin.php',
		'class-psmt-admin-post-helper.php',
		'class-psmt-admin-gallery-list-helper.php',
		'tools/debug/psmt-admin-debug-helper.php',
		'class-psmt-admin-edit-gallery-panel.php',
		'psmt-admin-misc.php',
	);

	if ( psmt_get_option( 'enable_debug' ) ) {
		$files[] = 'tools/class-psmt-media-debugger.php';
	}

	foreach ( $files as $file ) {
		require_once $path . $file;
	}

	do_action( 'psmt_admin_loaded' );
}
add_action( 'psmt_loaded', 'psmt_admin_load' );
