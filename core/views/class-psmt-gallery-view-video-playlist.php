<?php
/**
 * Gallery Video Playlist View.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Playlist view
 */
class PSMT_Gallery_View_Video_Playlist extends PSMT_Gallery_View {

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

		$this->id = 'playlist';
		$this->name = __( 'Video Playlist', 'psourcemediathek' );
	}

	/**
	 * Create/get singleton instance.
	 *
	 * @return PSMT_Gallery_View_Video_Playlist
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Render single gallery display(media list).
	 *
	 * @param PSMT_Gallery $gallery gallery object.
	 */
	public function display( $gallery ) {
		psmt_get_template( 'gallery/views/playlist-video.php' );
	}

	/**
	 * Not used. We plan to allow users override it in future.
	 *
	 * @param PSMT_Gallery $gallery gallery object.
	 */
	public function display_settings( $gallery ) {
	}

	/**
	 * Render view for activity media list.
	 *
	 * @param array $media_ids numeric media ids attached to the activity.
	 * @param int   $activity_id activity id.
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
			'buddypress/activity/views/playlist-video.php'
		);

		$located_template = psmt_locate_template( $templates, false );

		if ( $located_template ) {
			include $located_template;
		}
	}
}
