<?php
/**
 * Base media view.
 * Media views are used to display content for single media entry.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All media views must inherit this class.
 */
abstract class PSMT_Media_View {
	/**
	 * Display the view for media.
	 *
	 * @param PSMT_Media $media media object.
	 */
	public abstract function display( $media );
}
