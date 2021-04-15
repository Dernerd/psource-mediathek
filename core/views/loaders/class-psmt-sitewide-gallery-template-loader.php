<?php
/**
 * Template Loader for Sitewide galleries.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sitewide gallery template loader.
 */
class PSMT_Sitewide_Gallery_Template_Loader extends PSMT_Gallery_Template_Loader {

	/**
	 * Singleton instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->id   = 'default';
		$this->path = 'sitewide/';
	}

	/**
	 * Create/get singleton instance.
	 *
	 * @return PSMT_Sitewide_Gallery_Template_Loader
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load template for sitewide galleries.
	 */
	public function load_template() {

		$template = $this->path . 'home.php';
		$template = apply_filters( 'psmt_get_sitewide_gallery_template', $template );

		psmt_get_template( $template );
	}

}
