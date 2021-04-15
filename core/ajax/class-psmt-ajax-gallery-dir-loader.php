<?php
/**
 * PsourceMediathek Gallery directory ajax loader
 *
 * Loads gallery directory.
 *
 * @package    PsourceMediathek
 * @subpackage Core/Ajax
 * @copyright  Copyright (c) 2018, DerN3rd
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     DerN3rd
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit( 0 );

/**
 * Gallery directory loader
 */
class PSMT_Ajax_Gallery_Dir_Loader {

	/**
	 * Is booted?
	 *
	 * @var bool
	 */
	private static $booted = null;

	/**
	 * Boot the handler.
	 */
	public static function boot() {

		if ( self::$booted ) {
			return;
		}

		self::$booted = true;

		$self = new self();
		$self->setup();
	}

	/**
	 * Setup actions.
	 */
	public function setup() {

		// directory loop.
		add_action( 'wp_ajax_psmt_filter', array( $this, 'load_dir_list' ) );
		add_action( 'wp_ajax_nopriv_psmt_filter', array( $this, 'load_dir_list' ) );
	}

	/**
	 * Loads directory gallery list via ajax
	 */
	public function load_dir_list() {

		$type = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
		$page = absint( $_POST['page'] );

		$scope        = $_POST['scope'];
		$search_terms = $_POST['search_terms'];

		// for some theme, it is possibile to proide 'false' as search term.
		if ( 'false' === $search_terms ) {
			$search_terms = '';
		}

		// make the query and setup.
		psourcemediathek()->is_directory = true;

		$status = array();
		if ( psmt_is_active_status( 'public' ) ) {
			$status[] = 'public';
		}

		if ( is_user_logged_in() && psmt_is_active_status( 'loggedin' ) ) {
			$status[] = 'loggedin';
		}

		// get all public galleries, should we do type filtering.
		psourcemediathek()->the_gallery_query = new PSMT_Gallery_Query( array(
			'status'       => $status,
			'type'         => $type,
			'page'         => $page,
			'search_terms' => $search_terms,
		) );
		ob_start();
		psmt_get_template( 'gallery/loop-gallery.php' );
		$contents = ob_get_clean();

		if ( function_exists( 'bp_nouveau' ) ) {
			wp_send_json_success( array( 'contents' => $contents ) );
		} else {
			echo $contents;
		}
		exit( 0 );
	}
}
