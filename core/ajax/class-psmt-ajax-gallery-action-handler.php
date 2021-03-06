<?php
/**
 * PsourceMediathek Gallery Actions handler for admin.
 *
 * Handle various gallery actions.
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
 * Gallery actions handler.
 */
class PSMT_Ajax_Gallery_Action_Handler {

	/**
	 * Path to the template directory.
	 *
	 * @var string
	 */
	private $template_dir;

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
	 * Constructor.
	 */
	private function __construct() {
		$this->template_dir = psourcemediathek()->get_path() . 'admin/templates/';
	}

	/**
	 * Setup actions.
	 */
	public function setup() {
		// publish to activity.
		add_action( 'wp_ajax_psmt_publish_gallery_media', array( $this, 'publish_gallery_media' ) );
		add_action( 'wp_ajax_psmt_hide_unpublished_media', array( $this, 'hide_unpublished_media' ) );

		// Media delete.
		add_action( 'wp_ajax_psmt_delete_media', array( $this, 'delete_media' ) );
		// gallery/media management actions.
		add_action( 'wp_ajax_psmt_reorder_media', array( $this, 'reorder_media' ) );
		add_action( 'wp_ajax_psmt_bulk_update_media', array( $this, 'bulk_update_media' ) );
		add_action( 'wp_ajax_psmt_delete_gallery_cover', array( $this, 'delete_gallery_cover' ) );
		add_action( 'wp_ajax_psmt_update_gallery_details', array( $this, 'update_gallery_details' ) );
		add_action( 'wp_ajax_psmt_reload_bulk_edit', array( $this, 'reload_bulk_edit' ) );
		add_action( 'wp_ajax_psmt_reload_add_media', array( $this, 'reload_add_media' ) );

	}


	/**
	 * Publish gallery activity.
	 */
	public function publish_gallery_media() {

		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'publish' ) ) {
			// should we return or show error?
			return;
		}

		$gallery_id = absint( $_POST['gallery_id'] );

		if ( ! psmt_gallery_has_unpublished_media( $gallery_id ) ) {
			wp_send_json( array( 'message' => __( 'No media to publish.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		// check if user has permission.
		if ( ! psmt_user_can_publish_gallery_activity( $gallery_id ) ) {
			wp_send_json( array(
				'message' => __( "You don't have sufficient permission.", 'psourcemediathek' ),
				'error'   => 1
			) );
			exit( 0 );
		}

		$media_ids = psmt_gallery_get_unpublished_media( $gallery_id );

		$media_count = count( $media_ids );

		$gallery = psmt_get_gallery( $gallery_id );

		$type = $gallery->type;

		$type_name = _n( $type, $type . 's', $media_count );
		$user_link = psmt_get_user_link( get_current_user_id() );

		$gallery_url = psmt_get_gallery_permalink( $gallery );

		$gallery_link = '<a href="' . esc_url( $gallery_url ) . '" title="' . esc_attr( $gallery->title ) . '">' . psmt_get_gallery_title( $gallery ) . '</a>';


		$activity_id = psmt_gallery_record_activity( array(
			'gallery_id' => $gallery_id,
			'media_ids'  => $media_ids,
			'type'       => 'media_publish',
			'action'     => sprintf( __( '%s shared %d %s to %s ', 'psourcemediathek' ), $user_link, $media_count, $type_name, $gallery_link ),
			'content'    => '',
		) );


		if ( $activity_id ) {

			psmt_gallery_delete_unpublished_media( $gallery_id );

			wp_send_json( array(
				'message' => __( "Published to activity successfully.", 'psourcemediathek' ),
				'success' => 1
			) );
			exit( 0 );

		} else {

			wp_send_json( array(
				'message' => __( "There was a problem. Please try again later.", 'psourcemediathek' ),
				'error'   => 1
			) );
			exit( 0 );

		}

		//we are good, let us check if there are actually unpublished media
		//$unpublished_media =
		//get unpublished media ids
		//call _psmt_record_activity
		//how about success/failure

		exit( 0 );
	}

	public function hide_unpublished_media() {
		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'delete-unpublished' ) ) {
			// should we return or show error?
			return;
		}

		$gallery_id = absint( $_POST['gallery_id'] );

		if ( ! psmt_gallery_has_unpublished_media( $gallery_id ) ) {
			wp_send_json( array( 'message' => __( 'Nothing to hide.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		// check if user has permission.
		if ( ! psmt_user_can_publish_gallery_activity( $gallery_id ) ) {
			wp_send_json( array(
				'message' => __( "You don't have sufficient permission.", 'psourcemediathek' ),
				'error'   => 1
			) );
			exit( 0 );
		}

		psmt_gallery_delete_unpublished_media( $gallery_id );

		wp_send_json( array( 'message' => __( 'Successfully hidden!', 'psourcemediathek' ), 'success' => 1 ) );
		exit( 0 );
	}

	public function delete_media() {
		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'psmt-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		$media_id = absint( $_POST['media_id'] );
		$media    = psmt_get_media( $media_id );

		if ( ! $media ) {
			wp_send_json( array( 'message' => __( 'Invalid Media.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		if ( ! psmt_is_valid_media( $media->id ) ) {
			wp_send_json( array( 'message' => __( 'Invalid Media.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		if ( ! psmt_user_can_delete_media( $media_id ) ) {
			wp_send_json( array( 'message' => __( 'Unauthorized action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		psmt_delete_media( $media_id );

		wp_send_json( array( 'message' => __( 'Deleted.', 'psourcemediathek' ), 'success' => 1 ) );
		exit( 0 );

	}

	public function reorder_media() {

		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'psmt-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		// should we check for the permission? not here
		// array.
		$media_ids = $_POST['psmt-media-ids'];

		$media_ids = wp_parse_id_list( $media_ids );
		$media_ids = array_filter( $media_ids );
		$order     = count( $media_ids );

		foreach ( $media_ids as $media_id ) {

			if ( ! psmt_user_can_edit_media( $media_id ) ) {
				// unauthorized attempt.
				wp_send_json( array(
					'message' => __( "You don't have permission to update!", 'psourcemediathek' ),
					'error'   => 1
				) );
				exit( 0 );

			}
			// if we are here, let us update the order.
			psmt_update_media_order( $media_id, $order );
			$order --;

		}

		if ( $media_id ) {
			// mark the gallery assorted, we use it in PSMT_Media_Query to see what should be the default order.
			$media = psmt_get_media( $media_id );
			// mark the gallery as sorted.
			psmt_mark_gallery_sorted( $media->gallery_id );
		}

		wp_send_json( array( 'message' => __( 'Updated.', 'psourcemediathek' ), 'success' => 1 ) );
		exit( 0 );
	}

	public function bulk_update_media() {

		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'psmt-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		if ( ! $_POST['psmt-editing-media-ids'] ) {
			return;
		}

		$gallery_id = absint( $_POST['gallery_id'] );
		$gallery    = psmt_get_gallery( $gallery_id );

		if ( ! $gallery_id || ! $gallery ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		$message = '';

		$media_ids = $_POST['psmt-editing-media-ids'];
		$media_ids = wp_parse_id_list( $media_ids );
		$media_ids = array_filter( $media_ids );

		$bulk_action = false;

		if ( ! empty( $_POST['psmt-edit-media-bulk-action'] ) ) {
			// we are leaving this to allow future enhancements with other bulk action and not restricting to delete only.
			$bulk_action = $_POST['psmt-edit-media-bulk-action'];
		}

		foreach ( $media_ids as $media_id ) {
			// check what action should we take?
			// 1. check if $bulk_action is set? then we may ned to check for deletion
			// otherwise, just update the details :).
			if ( $bulk_action == 'delete' && ! empty( $_POST['psmt-delete-media-check'][ $media_id ] ) ) {

				// delete and continue
				// check if current user can delete?
				if ( ! psmt_user_can_delete_media( $media_id ) ) {
					// if the user is unable to delete media, should we just continue the loop or breakout and redirect back with error?
					// I am in favour of showing error.
					$success = 0;

					wp_send_json( array( 'message' => __( 'Not allowed to delete!', 'psourcemediathek' ), 'error' => 1 ) );
					exit( 0 );
				}

				// if we are here, let us delete the media.
				psmt_delete_media( $media_id );

				// it will do for each media, that is not  good thing btw.
				$message = __( 'Deleted successfully!', 'psourcemediathek' );
				$success = 1;
				continue;
			}

			// since we already handled delete for the media checked above,
			// we don't want to do it for the other media hoping that the user was performing bulk delete and not updating the media info.
			if ( $bulk_action == 'delete' ) {
				continue;
			}

			// is it media update.
			$media_title = $_POST['psmt-media-title'][ $media_id ];

			$media_description = $_POST['psmt-media-description'][ $media_id ];

			$status = $_POST['psmt-media-status'][ $media_id ];

			// if we are here, It must not be a bulk action.
			$media_info = array(
				'id'          => $media_id,
				'title'       => $media_title,
				'description' => $media_description,
				// 'type'		=> $type,
				'status'      => $status,
			);

			psmt_update_media( $media_info );

		}

		if ( ! $bulk_action ) {
			$message = __( 'Updated!', 'psourcemediathek' );
		} elseif ( ! $message ) {
			$message = __( 'Please select media to apply bulk actions.', 'psourcemediathek' );
		}

		psourcemediathek()->current_gallery = $gallery;

		psourcemediathek()->the_media_query = new PSMT_Media_Query( array(
			'gallery_id' => $gallery_id,
			'per_page'   => - 1,
			'nopaging'   => true,
		) );

		global $post;

		$bkp_post = $post;

		ob_start();
		require_once $this->template_dir . 'gallery/edit-media.php';

		$contents = ob_get_clean();
		$post     = $bkp_post;
		// remember to add content too.
		wp_send_json( array( 'message' => $message, 'success' => 1, 'contents' => $contents ) );

		exit( 0 );
	}

	public function delete_gallery_cover() {

		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'psmt-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		$gallery = psmt_get_gallery( absint( $_REQUEST['gallery_id'] ) );

		if ( ! $gallery ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		// we may want to allow passing of component from the form in future!
		if ( ! psmt_user_can_delete_gallery( $gallery ) ) {

			wp_send_json( array(
				'message' => __( "You don't have permission to delete this cover!", 'psourcemediathek' ),
				'error'   => 1
			) );
			exit( 0 );

		}

		// we always need to delete this.
		$cover_id = psmt_get_gallery_cover_id( $gallery->id );
		psmt_delete_gallery_cover_id( $gallery->id );

		psmt_delete_media( $cover_id );

		wp_send_json( array(
			'message' => __( 'Cover deleted', 'psourcemediathek' ),
			'success' => 1,
			'cover'   => psmt_get_gallery_cover_src( 'thumbnail', $gallery->id ),
		) );
		exit( 0 );

	}

	public function update_gallery_details() {

		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'psmt-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		$gallery_id = absint( $_POST['psmt-gallery-id'] );

		if ( ! $gallery_id ) {
			return;
		}

		// check for permission
		// we may want to allow passing of component from the form in future!
		if ( ! psmt_user_can_edit_gallery( $gallery_id ) ) {

			wp_send_json( array(
				'message' => __( "You don't have permission to update.", 'psourcemediathek' ),
				'error'   => 1,
			) );
			exit( 0 );
		}


		$description = $_POST['psmt-gallery-description'];

		$errors = array();


		// give opportunity to other plugins to add their own validation errors.
		$validation_errors = apply_filters( 'psmt-edit-gallery-field-validation', $errors, $_POST );

		if ( ! empty( $validation_errors ) ) {
			// let us add the validation error and return back to the earlier page.
			$message = join( '\r\n', $validation_errors );

			wp_send_json( array( 'message' => $message, 'error' => 1 ) );

			exit( 0 );

		}

		// let us create gallery.
		$gallery_id = psmt_update_gallery( array(
			'description' => $description,
			'id'          => $gallery_id,
		) );


		if ( ! $gallery_id ) {

			wp_send_json( array( 'message' => __( 'Unable to update gallery!', 'psourcemediathek' ), 'error' => 1 ) );

			exit( 0 );

		}

		wp_send_json( array( 'message' => __( 'Gallery updated successfully!', 'psourcemediathek' ), 'success' => 1 ) );

		exit( 0 );


	}

	public function reload_bulk_edit() {
		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'psmt-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		$gallery_id = absint( $_POST['gallery_id'] );
		$gallery    = psmt_get_gallery( $gallery_id );

		if ( ! $gallery_id || ! $gallery ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}


		if ( ! psmt_user_can_edit_gallery( $gallery ) ) {
			wp_send_json( array(
				'message' => __( "You don't have permission to update gallery.", 'psourcemediathek' ),
				'error'   => 1
			) );
			exit( 0 );
		}

		//show the form
		psourcemediathek()->current_gallery = $gallery;

		psourcemediathek()->the_media_query = new PSMT_Media_Query( array(
			'gallery_id' => $gallery_id,
			'per_page'   => - 1,
			'nopaging'   => true,
		) );

		global $post;

		$bkp_post = $post;

		ob_start();
		require_once $this->template_dir . 'gallery/edit-media.php';

		$contents = ob_get_clean();

		$post = $bkp_post;

		wp_send_json( array( 'message' => __( 'Updated.', 'psourcemediathek' ), 'success' => 1, 'contents' => $contents ) );

		exit( 0 );

	}

	public function reload_add_media() {
		// verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'psmt-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}

		$gallery_id = absint( $_POST['gallery_id'] );
		$gallery    = psmt_get_gallery( $gallery_id );

		if ( ! $gallery_id || ! $gallery ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'psourcemediathek' ), 'error' => 1 ) );
			exit( 0 );
		}


		if ( ! psmt_user_can_upload( $gallery->component, $gallery->component_id, $gallery ) ) {
			wp_send_json( array(
				'message' => __( "You don't have permission to upload.", 'psourcemediathek' ),
				'error'   => 1,
			) );
			exit( 0 );
		}

		//show the form
		psourcemediathek()->current_gallery = $gallery;
		psourcemediathek()->the_media_query = new PSMT_Media_Query( array(
			'gallery_id' => $gallery_id,
			'per_page'   => - 1,
			'nopaging'   => true,
		) );

		global $post;

		$bkp_post = $post;

		ob_start();
		require_once $this->template_dir . 'gallery/add-media.php';

		$contents = ob_get_clean();

		$post = $bkp_post;

		wp_send_json( array( 'message' => __( 'Updated.', 'psourcemediathek' ), 'success' => 1, 'contents' => $contents ) );
		exit( 0 );

	}

}
