<?php
/**
 * Single audio view.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The benefit of using a view class is the control that
 * it allows to change the view generation without worrying about template changes.
 */
class PSMT_Media_View_Audio extends PSMT_Media_View {

	/**
	 * Display the audio.
	 *
	 * @param PSMT_Media $media media object.
	 */
	public function display( $media ) {
		psmt_get_template( 'gallery/media/views/audio.php' );
	}

}
