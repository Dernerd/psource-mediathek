<?php
/**
 * PsourceMediathek uploader shortcode.
 *
 * @package psourcemediathek.
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PsourceMediathek uploader shortcode handler.
 *
 * Handles [psmt-uploader] shortcode.
 *
 * @param array  $atts see the function for details.
 * @param string $content n/a.
 *
 * @return string
 */
function psmt_shortcode_uploader( $atts = array(), $content = '' ) {

	$default = array(
		'gallery_id'         => 0,
		'component'          => psmt_get_current_component(),
		'component_id'       => psmt_get_current_component_id(),
		'type'               => '',
		'status'             => psmt_get_default_status(),
		'view'               => '',
		'selected'           => 0,
		'skip_gallery_check' => 0,
		'label_empty'        => __( 'Please select a gallery', 'psourcemediathek' ),
		'show_error'         => 1,
	);

	$atts = shortcode_atts( $default, $atts );
	// dropdown list of galleries to allow user select one.
	$view = 'list';

	if ( ! empty( $atts['gallery_id'] ) && is_numeric( $atts['gallery_id'] ) ) {
		$view = 'single';// single gallery uploader.
		// override component and $component id.
		$gallery = psmt_get_gallery( $atts['gallery_id'] );

		if ( ! $gallery ) {
			return __( 'Nonexistent gallery should not be used', 'psourcemediathek' );
		}

		// reset.
		$atts['component']    = $gallery->component;
		$atts['component_id'] = $gallery->component_id;
		$atts['type']         = $gallery->type;
	}

	// the user must be able to upload to current component or gallery.
	$can_upload = false;

	if ( psmt_user_can_upload( $atts['component'], $atts['component_id'], $atts['gallery_id'] ) ) {
		$can_upload = true;
	}

	if ( ! $can_upload && $atts['show_error'] ) {
		return __( 'Sorry, you are not allowed to upload here.', 'psourcemediathek' );
	}

	// if we are here, the user can upload
	// we still have one issue,
	// what if the user has not created any gallery and the admin intends to allow the user to upload to their created gallery.
	$atts['context'] = 'shortcode'; // from where it is being uploaded.

	$atts['view'] = $view;

	ob_start();
	// passing the 2nd arg makes all these variables available to the loaded file.
	psmt_get_template( 'shortcodes/uploader.php', $atts );

	$content = ob_get_clean();

	return $content;
}
add_shortcode( 'psmt-uploader', 'psmt_shortcode_uploader' );
