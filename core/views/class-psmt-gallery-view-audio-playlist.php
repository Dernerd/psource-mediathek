<?php
/**
 * Audio playlist view.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Audio playlist gallery view.
 */
class PSMT_Gallery_View_Audio_Playlist extends PSMT_Gallery_View {

	/**
	 * Singleton instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct();

		$this->id   = 'playlist';
		$this->name = __( 'Audio Playlist', 'psourcemediathek' );
	}

	/**
	 * Create/get singleton instance.
	 *
	 * @return PSMT_Gallery_View_Audio_Playlist
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Display single gallery media as playlist
	 *
	 * @param PSMT_Gallery $gallery gallery object.
	 */
	public function display( $gallery ) {
		psmt_get_template( 'gallery/views/playlist-audio.php' );
	}

	/**
	 * Display audio playlist for activity
	 *
	 * @param int[] $media_ids media ids.
	 * @param int   $activity_id activity id.
	 *
	 * @return null
	 */
	public function activity_display( $media_ids = array(), $activity_id = 0 ) {

		if ( ! $media_ids ) {
			return;
		}

		if ( ! $activity_id ) {
			$activity_id = bp_get_activity_id();
		}

		// we will use include to load found template file,
		// the file will have $media_ids available.
		$templates = array(
			'buddypress/activity/views/playlist-audio.php'
		);

		$located_template = psmt_locate_template( $templates, false );

		if ( $located_template ) {
			include $located_template;
		}
	}

}
