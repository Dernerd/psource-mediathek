<?php
/**
 * Single photo view.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single photo.
 */
class PSMT_Media_View_Photo extends PSMT_Media_View {
	/**
	 * Display photo.
	 *
	 * @param PSMT_Media $media media object.
	 */
	public function display( $media ) {
		psmt_get_template( 'gallery/media/views/photo.php' );
	}

}
