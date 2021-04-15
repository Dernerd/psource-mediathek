<?php
/**
 * Single video view.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single video view.
 */
class PSMT_Media_View_Video extends PSMT_Media_View {

	/**
	 * Display video.
	 *
	 * @param PSMT_Media $media media object.
	 */
	public function display( $media ) {
		$media = psmt_get_media( $media );

		$template = $media->is_oembed ? 'gallery/media/views/oembed.php' : 'gallery/media/views/video.php';
		psmt_get_template( $template );
	}

}
