<?php
/**
 * Template Loader for Groups
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Groups template loader.
 */
class PSMT_Groups_Gallery_Template_Loader extends PSMT_Gallery_Template_Loader {

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

		$this->id   = 'default';
		$this->path = 'buddypress/groups/';
	}

	/**
	 * Create/get singleton instance.
	 *
	 * @return PSMT_Groups_Gallery_Template_Loader
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load template for groups galleries.
	 */
	public function load_template() {

		$template = $this->path . 'home.php';
		$template = apply_filters( 'psmt_get_groups_gallery_template', $template );

		psmt_get_template( $template );
	}

}
