<?php
/**
 * Create gallery shortcode
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Handle shortcode for create gallery form [psmt-create-gallery]
 *
 * @param array  $atts allowed atts.
 * @param string $content n/a.
 *
 * @return null|string
 */
function psmt_shortcode_create_gallery( $atts = array(), $content = null ) {

	$defaults = array();
	// do not show it to the non logged user.
	if ( ! is_user_logged_in() ) {
		return $content;
	}

	ob_start();

	psmt_get_template( 'shortcodes/create-gallery.php' );

	$content = ob_get_clean();

	return $content;
}
add_shortcode( 'psmt-create-gallery', 'psmt_shortcode_create_gallery' );
