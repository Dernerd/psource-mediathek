<?php
/**
 * Single doc view.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Doc view.
 */
class PSMT_Media_View_Docs extends PSMT_Media_View {

	/**
	 * Display doc.
	 *
	 * @param PSMT_Media $media media object.
	 */
	public function display( $media ) {

		psmt_get_template( 'gallery/media/views/doc.php' );

	}

}
