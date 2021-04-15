<?php

/**
 * Loads PsourceMediathek core files.
 *
 * @package PsourceMediathek
 */

/**
 * Core loader
 */
class PSMT_Core_Loader {

	/**
	 * Path to the plugin directory
	 *
	 * @var string
	 */
	private $path = '';


	/**
	 * Constructor, sets up path.
	 */
	public function __construct() {
		$this->path = psourcemediathek()->get_path();
	}


	/**
	 * Load all kind of dependencies
	 */
	public function load() {

		$this->load_core();
		$this->load_widgets();
		$this->load_shortcodes();

		$this->load_ajax_handlers();
	}


	/**
	 * Load core dependencies.
	 */
	public function load_core() {

		$files = array(
			'core/common/psmt-feedback-functions.php',
			'core/common/psmt-misc-functions.php',
			'core/common/psmt-common-functions.php',
			'core/common/class-psmt-hooks-helper.php',
			'core/common/class-psmt-cached-media-query.php',
			'core/common/class-psmt-gallery-query.php',
			'core/common/class-psmt-media-query.php',
			'core/common/psmt-nav-functions.php',
			'core/psmt-post-type.php',
			'core/class-psmt-deletion-actions-mapper.php',
			'core/common/class-psmt-taxonomy.php',
			'core/common/class-psmt-menu.php',
			'core/common/class-psmt-features.php',
			'core/common/psmt-taxonomy-functions.php',
			// Gallery related.
			'core/gallery/class-psmt-gallery.php',
			'core/gallery/psmt-gallery-conditionals.php',
			'core/gallery/psmt-gallery-cover-templates.php',
			'core/gallery/psmt-gallery-functions.php',
			'core/gallery/psmt-gallery-link-template.php',
			'core/gallery/psmt-gallery-meta.php',
			'core/gallery/psmt-gallery-screen.php',
			'core/gallery/psmt-gallery-template-tags.php',
			'core/gallery/psmt-gallery-hooks.php',
			'core/gallery/psmt-gallery-actions.php',
			'core/gallery/psmt-gallery-activity.php',
			'core/gallery/psmt-gallery-template.php',
			// Media related.
			'core/media/class-psmt-media-importer.php',
			'core/media/class-remote-media-parser.php',
			'core/media/psmt-media-functions.php',
			'core/media/psmt-remote-media-functions.php',
			'core/media/psmt-media-meta.php',
			'core/media/class-psmt-media.php',
			'core/media/psmt-media-template-tags.php',
			'core/media/psmt-media-link-templates.php',
			'core/media/psmt-media-actions.php',
			'core/media/psmt-media-cover-template.php',
			'core/media/psmt-media-activity.php',
			'core/media/psmt-media-hooks.php',
			// Views related, gallery views.
			'core/views/class-psmt-gallery-view.php',
			'core/views/class-psmt-gallery-view-default.php',
			'core/views/class-psmt-gallery-view-audio-playlist.php',
			'core/views/class-psmt-gallery-view-video-playlist.php',
			'core/views/class-psmt-gallery-view-list.php',
			'core/views/psmt-gallery-view-functions.php',
			//
			// Views: media viewer.
			'core/media/views/class-psmt-media-view.php',
			'core/media/views/class-psmt-media-view-photo.php', // for image files
			'core/media/views/class-psmt-media-view-doc.php', // for doc files
			'core/media/views/class-psmt-media-view-video.php', // for video files
			'core/media/views/class-psmt-media-view-audio.php', // for audio files
			// API.
			'core/api/psmt-actions-api.php',
			'core/api/psmt-api.php',
			'core/psmt-hooks.php',
			// User related.
			'core/users/psmt-user-meta.php',
			'core/users/psmt-user-functions.php',
			'core/users/psmt-user-hooks.php',
			//
			// Asset loading.
			'assets/psmt-assets-loader.php',
			// Template/Permissions.
			'core/psmt-template-helpers.php',
			'core/psmt-permissions.php',
			// Storage related.
			'core/storage/psmt-storage-functions.php',
			'core/storage/psmt-storage-space-stats-functions.php',
			'core/storage/class-psmt-storage-manager.php',
			'core/storage/class-psmt-local-storage.php',
			// Theme compat.
			'core/psmt-theme-compat.php',
			'psmt-init.php',
			'psmt-core-component.php',

			// cron job.
			'core/common/psmt-cron.php',
		);

		if ( is_admin() ) {
			$files[] = 'admin/psmt-admin-loader.php';
		}

		if ( psourcemediathek()->is_bp_active() ) {
			$files[] = 'modules/buddypress/psmt-bp-loader.php';
		}

		$path = psourcemediathek()->get_path();

		foreach ( $files as $file ) {
			require_once $path . $file;
		}

	}


	/**
	 * Load ajax handlers
	 */
	public function load_ajax_handlers() {

		if ( ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		$files = array(
			'core/ajax/psmt-ajax.php',
			'core/ajax/class-psmt-ajax-remote-media-handler.php',
			'core/ajax/class-psmt-ajax-activity-post-handler.php',
			'core/ajax/class-psmt-ajax-gallery-action-handler.php',
			'core/ajax/class-psmt-ajax-gallery-dir-loader.php',
			'core/ajax/class-psmt-ajax-comment-helper.php',
			'core/ajax/class-psmt-ajax-lightbox-helper.php',
		);

		$path = psourcemediathek()->get_path();

		foreach ( $files as $file ) {
			require_once $path . $file;
		}

		// initialize.
		PSMT_Ajax_Helper::get_instance();
		PSMT_Ajax_Remote_Media_Handler::boot();
		PSMT_Ajax_Activity_Post_Handler::boot();
		// commenting.
		PSMT_Ajax_Comment_Helper::get_instance();

		// Lightbox handler.
		new PSMT_Ajax_Lightbox_Helper();

		PSMT_Ajax_Gallery_Dir_Loader::boot();
		PSMT_Ajax_Gallery_Action_Handler::boot();
	}


	/**
	 * Load comments handlers
	 */
	public function load_comment_handlers() {
		// comment.
		require_once $this->path . 'core/comments/psmt-comment-functions.php';
		require_once $this->path . 'core/comments/class-psmt-comment.php';
		require_once $this->path . 'core/comments/class-psmt-comments-helper.php';
		require_once $this->path . 'core/comments/psmt-comment-template-tags.php';
	}


	/**
	 * Load shortcode related files.
	 */
	private function load_shortcodes() {

		require_once $this->path . 'core/shortcodes/psmt-shortcode-functions.php';
		require_once $this->path . 'core/shortcodes/psmt-shortcode-gallery-list.php';
		require_once $this->path . 'core/shortcodes/psmt-shortcode-media-list.php';
		require_once $this->path . 'core/shortcodes/psmt-shortcode-create-gallery.php';
		require_once $this->path . 'core/shortcodes/psmt-shortcode-media-uploader.php';

	}


	/**
	 * Load Widgets
	 */
	private function load_widgets() {

		require_once $this->path . 'core/widgets/psmt-widget-functions.php';
		require_once $this->path . 'core/widgets/psmt-widget-gallery.php';
		require_once $this->path . 'core/widgets/psmt-widget-media.php';

	}

}
