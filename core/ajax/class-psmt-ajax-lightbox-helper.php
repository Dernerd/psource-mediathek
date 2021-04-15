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

// Exit if the file is accessed directly over web.
defined( 'ABSPATH' ) || exit( 0 );


/**
 * PsourceMediathek Ajax helper
 * Not implementing it as singleton, if you need to add custom handler, attach your own with higher priority
 */
class PSMT_Ajax_Lightbox_Helper {

	/**
	 * Template directory(cached)
	 *
	 * @var string
	 */
	private $template_dir;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->template_dir = psourcemediathek()->get_path() . 'admin/templates/';

		$this->setup_hooks();
	}

	/**
	 * Setup.
	 */
	private function setup_hooks() {
		// activity media.
		add_action( 'wp_ajax_psmt_fetch_activity_media', array( $this, 'fetch_activity_media' ) );
		add_action( 'wp_ajax_nopriv_psmt_fetch_activity_media', array( $this, 'fetch_activity_media' ) );
		// for lightbox when clicked on gallery.
		add_action( 'wp_ajax_psmt_fetch_gallery_media', array( $this, 'fetch_gallery_media' ) );
		add_action( 'wp_ajax_nopriv_psmt_fetch_gallery_media', array( $this, 'fetch_gallery_media' ) );

		add_action( 'wp_ajax_psmt_lightbox_fetch_media', array( $this, 'fetch_media' ) );
		add_action( 'wp_ajax_nopriv_psmt_lightbox_fetch_media', array( $this, 'fetch_media' ) );
		add_action( 'wp_ajax_psmt_update_lightbox_media', array( $this, 'update_lightbox_media' ) );

		add_action( 'wp_ajax_psmt_reload_lightbox_media', array( $this, 'reload_lightbox_media' ) );
		add_action( 'wp_ajax_nopriv_psmt_reload_lightbox_media', array( $this, 'reload_lightbox_media' ) );


	}

	/**
	 * Get media fro activity
	 */
	public function fetch_activity_media() {

		// do we need nonce validation for this request too?
		$items = array();
		$activity_id = $_POST['activity_id'];

		if ( ! $activity_id ) {
			exit( 0 );
		}

		$media_ids = psmt_activity_get_attached_media_ids( $activity_id );

		if ( empty( $media_ids ) ) {
			$media_ids = (array) psmt_activity_get_media_id( $activity_id );
		}

		if ( empty( $media_ids ) ) {

			array_push( $items, __( 'Sorry, Nothing found!', 'psourcemediathek' ) );

			wp_send_json( array( 'items' => $items ) );
			exit( 0 );
		}

		$gallery_id = psmt_activity_get_gallery_id( $activity_id );
		$gallery    = psmt_get_gallery( $gallery_id );

		if ( 'groups' === $gallery->component && function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
			//if( empty( buddypress()->groups))
		}

		$media_query = new PSMT_Media_Query( array( 'in' => $media_ids, 'per_page' => - 1, 'nopaging' => true ) );

		if ( $media_query->have_media() ) :
			?>

			<?php while ( $media_query->have_media() ) : $media_query->the_media(); ?>

			<?php $items[] = array( 'src' => $this->get_media_lightbox_entry(), 'id' => psmt_get_media_id() ); ?>

		<?php endwhile; ?>

		<?php endif; ?>
		<?php psmt_reset_media_data(); ?>
		<?php

		wp_send_json( array( 'items' => $items ) );
		exit( 0 );
	}

	/**
	 * Fetch for gallery.
	 */
	public function fetch_gallery_media() {

		// do we need nonce validation for this request too? no.
		$items = array();

		$gallery_id = absint( $_POST['gallery_id'] );
		$gallery    = psmt_get_gallery( $gallery_id );

		if ( ! $gallery_id || empty( $gallery ) ) {
			exit( 0 );
		}

		$statuses = psmt_get_accessible_statuses( $gallery->component, $gallery->component_id, get_current_user_id() );

		$media_query = new PSMT_Media_Query( array(
			'gallery_id' => $gallery_id,
			'per_page'   => - 1,
			'nopaging'   => true,
			'status'     => $statuses,
		) );

		if ( $media_query->have_media() ) :
			?>

			<?php while ( $media_query->have_media() ) : $media_query->the_media(); ?>

			<?php $items[] = array( 'src' => $this->get_media_lightbox_entry(), 'id' => psmt_get_media_id() ); ?>

		<?php endwhile; ?>

		<?php endif; ?>
		<?php psmt_reset_media_data(); ?>
		<?php

		wp_send_json( array( 'items' => $items ) );
		exit( 0 );
	}

	/**
	 * Fetch individual media or media list.
	 */
	public function fetch_media() {
		// do we need nonce validation for this request too? no.
		$items = array();

		$media_ids = $_POST['media_ids'];
		$media_ids = wp_parse_id_list( $media_ids );

		if ( empty( $media_ids ) ) {
			exit( 0 );
		}


		$media_query = new PSMT_Media_Query( array(
			'in'       => $media_ids,
			'per_page' => - 1,
			'nopaging' => true,
			'orderby'  => 'none',
		) );
		$user_id     = get_current_user_id();

		if ( $media_query->have_media() ) :
			?>


			<?php while ( $media_query->have_media() ) : $media_query->the_media(); ?>

			<?php
			if ( ! psmt_user_can_view_media( psmt_get_media_id(), $user_id ) ) {
				continue;
			}

			?>
			<?php $items[ psmt_get_media_id() ] = array( 'id'=> psmt_get_media_id(), 'src' => $this->get_media_lightbox_entry() ); ?>

		<?php endwhile; ?>

		<?php endif; ?>

		<?php psmt_reset_media_data(); ?>
		<?php
		// reorder items according to our ids order, WP resets to desc order.
		$new_items = array();
		// it may not be the best way but it seems to be the only way to make it work where we should not order media at all.
		foreach ( $media_ids as $media_id ) {
			if ( isset( $items[ $media_id ] ) ) {
				$new_items[] = $items[ $media_id ];
			}
		}

		wp_send_json( array( 'items' => $new_items ) );
		exit( 0 );
	}


	/**
	 * Update media details for inline editing via ajax.
	 */
	public function update_lightbox_media() {
		if ( ! wp_verify_nonce( $_POST['psmt-nonce'], 'psmt-lightbox-edit-media' ) ) {
			wp_send_json_error( array( 'message' => __( 'Not authorized!', 'psourcemediathek' ) ) );
		}
		$media_id = absint( $_POST['psmt-media-id'] );

		$media = psmt_get_media( $media_id );

		if ( ! $media ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request!', 'psourcemediathek' ) ) );
		}

		// check permissions.
		if ( ! psmt_user_can_edit_media( $media_id, get_current_user_id() ) ) {
			wp_send_json_error( array( 'message' => __( 'Not authorized!', 'psourcemediathek' ) ) );
		}

		// if we are here, check the title, description.
		// make sure title is given and the status is valid.
		$title       = isset( $_POST['psmt-media-title'] ) ? $_POST['psmt-media-title'] : '';
		$description = isset( $_POST['psmt-media-description'] ) ? $_POST['psmt-media-description'] : '';
		$status      = isset( $_POST['psmt-media-status'] ) ? $_POST['psmt-media-status'] : '';

		if ( empty( $title ) ) {
			wp_send_json_error( array( 'message' => __( "Title can't be empty.", 'psourcemediathek' ) ) );
		}


		if ( ! psmt_component_supports_status( $media->component, $status ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid status.', 'psourcemediathek' ) ) );
		}

		// if we are here, let us update.
		$media_info = array(
			'id'          => $media_id,
			'title'       => $title,
			'description' => $description,
			'status'      => $status,
		);

		$id = psmt_update_media( $media_info );

		// Setup current media.
		psourcemediathek()->current_media = psmt_get_media( $id );

		if ( $id ) {
			wp_send_json_success( array(
				'message' => __( 'Updated.', 'psourcemediathek' ),
				'content' => $this->get_media_lightbox_entry(),
			) );
		}

		// if we are here, it was an error.
		wp_send_json_error( array( 'message' => __( 'There was a problem. Please try again later!', 'psourcemediathek' ) ) );

	}

	/**
	 * Resend the html for the given media
	 */
	public function reload_lightbox_media() {
		$media_id = isset( $_POST['media_id'] ) ? absint( $_POST['media_id'] ) : 0;

		if ( ! psmt_user_can_view_media( $media_id, get_current_user_id() ) ) {
			wp_send_json_error( __( 'Permission denied.', 'psourcemediathek' ) );
		}
		$media = psmt_get_media( $media_id );

		if ( ! $media || ! psmt_is_valid_media( $media_id ) ) {
			wp_send_json_error( __( 'An error occurred. Please try again later.', 'psourcemediathek' ) );
		}

		// Setup current media.
		psourcemediathek()->current_media = $media;
		wp_send_json_success( array(
			'content' => $this->get_media_lightbox_entry(),
		) );
	}
	/**
	 * Entry for individual media.
	 *
	 * @return string
	 */
	private function get_media_lightbox_entry() {

		if ( psmt_get_option( 'lightbox_media_only' ) ) {
			$template = 'gallery/media/views/lightbox.php';
		} else {
			$template = 'gallery/media/views/lightbox-comment.php';
		}

		$located_template = apply_filters( 'psmt_lightbox_template', psmt_locate_template( array( $template ), false ) );

		if ( ! is_readable( $located_template ) ) {
			return '';
		}

		ob_start();

		require $located_template;

		return ob_get_clean();
	}

}