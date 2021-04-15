<?php
/**
 * PsourceMediathek Activity comment handler(single media/gallery).
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
 * PsourceMediathek Ajax Comment Helper, handles posting of activity comment/replies on the Gallery/media
 */
class PSMT_Ajax_Comment_Helper {
	/**
	 * Singleton instance
	 *
	 * @var PSMT_Ajax_Comment_Helper
	 */
	private static $instance;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Get the sngleton instance.
	 *
	 * @return PSMT_Ajax_Comment_Helper
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup hooks.
	 */
	private function setup_hooks() {
		add_action( 'wp_ajax_psmt_add_comment', array( $this, 'post_comment' ) );
		add_action( 'wp_ajax_psmt_add_reply', array( $this, 'post_reply' ) );
	}

	/**
	 * Post a gallery or media Main comment on single page
	 */
	public function post_comment() {
		// This is BuddyPress dependent.
		if ( ! function_exists( 'buddypress' ) ) {
			exit( 0 );
		}
		// Bail if not a POST action.
		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		// Check the nonce.
		check_admin_referer( 'post_update', '_wpnonce_post_update' );

		if ( ! is_user_logged_in() ) {
			exit( '-1' );
		}

		$psmt_type = $_POST['psmt-type'];
		$psmt_id   = $_POST['psmt-id'];

		if ( empty( $_POST['content'] ) ) {
			exit( '-1<div id="message" class="error"><p>' . __( 'Please enter some content to post.', 'psourcemediathek' ) . '</p></div>' );
		}

		$activity_id = 0;

		if ( empty( $_POST['object'] ) && bp_is_active( 'activity' ) ) {

			// we are preventing this comment to be set as the user's lastes_update.
			$user_id = bp_loggedin_user_id();

			$old_latest_update = bp_get_user_meta( $user_id, 'bp_latest_update', true );

			$activity_id = bp_activity_post_update( array( 'content' => $_POST['content'] ) );
			// restore.
			if ( ! empty( $old_latest_update ) ) {
				bp_update_user_meta( $user_id, 'bp_latest_update', $old_latest_update );
			}
		} elseif (  'groups' === $_POST['object'] ) {
			if ( ! empty( $_POST['item_id'] ) && bp_is_active( 'groups' ) ) {
				$activity_id = groups_post_update( array(
					'content'  => $_POST['content'],
					'group_id' => $_POST['item_id'],
				) );
			}
		} else {
			$activity_id = apply_filters( 'bp_activity_custom_update', $_POST['object'], $_POST['item_id'], $_POST['content'] );
		}

		if ( empty( $activity_id ) ) {
			exit( '-1<div id="message" class="error"><p>' . __( 'There was a problem posting your update, please try again.', 'psourcemediathek' ) . '</p></div>' );
		}
		$status = '';
		$user_id = 0;
		$action_type = '';

		// if we have got activity id, let us add a meta key.
		if ( 'gallery' === $psmt_type ) {

			psmt_activity_update_gallery_id( $activity_id, $psmt_id );
			psmt_activity_update_activity_type( $activity_id, 'gallery_comment' );
			psmt_activity_update_context( $activity_id, 'gallery' );

			$status = psmt_get_gallery_status( $psmt_id );
			$user_id = psmt_get_gallery_creator_id( $psmt_id );
			$action_type = 'psmt_gallery_comment';
		} elseif ( 'media' === $psmt_type ) {

			$media = psmt_get_media( $psmt_id );

			if ( ! $media ) {
				die( '-1' );
			}
			psmt_activity_update_gallery_id( $activity_id, $media->gallery_id );
			psmt_activity_update_media_id( $activity_id, $psmt_id );
			psmt_activity_update_activity_type( $activity_id, 'media_comment' );
			psmt_activity_update_context( $activity_id, 'media' );

			// also we need to keep the parent gallery id for caching.
			$status = psmt_get_media_status( $media );
			$user_id = psmt_get_media_creator_id( $media );
			$action_type = 'psmt_media_comment';
		}

		$activity = new BP_Activity_Activity( $activity_id );
		// $activity->component = buddypress()->psourcemediathek->id;
		$activity->type = 'psmt_media_upload';
		$activity->save();

		// save activity privacy.
		if ( $status ) {
			$status_object = psmt_get_status_object( $status );

			if ( $status_object ) {
				bp_activity_update_meta( $activity->id, 'activity-privacy', $status_object->activity_privacy );
			}
		}

		if ( function_exists( 'psmt_send_bp_notification' ) ) {
			psmt_send_bp_notification( $user_id, $action_type, $psmt_id, get_current_user_id() );
		}

		// create a shadow comment.
		psmt_activity_create_comment_for_activity( $activity_id );

		if ( bp_has_activities( 'include=' . $activity_id ) ) {
			while ( bp_activities() ) {
				bp_the_activity();
				psmt_locate_template( array( 'buddypress/activity/entry.php' ), true );
			}
		}

		exit;
	}

	/**
	 * Posts new Activity comments received via a POST request.
	 *
	 * @global BP_Activity_Template $activities_template
	 * @return string HTML
	 * @since BuddyPress (1.2)
	 */
	public function post_reply() {

		if ( ! function_exists( 'buddypress' ) ) {
			exit( 0 );
		}

		global $activities_template;

		$bp = buddypress();

		// Bail if not a POST action.
		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		// Check the nonce.
		check_admin_referer( 'new_activity_comment', '_wpnonce_new_activity_comment' );

		if ( ! is_user_logged_in() ) {
			exit( '-1' );
		}

		$feedback = __( 'There was an error posting your reply. Please try again.', 'psourcemediathek' );

		if ( empty( $_POST['content'] ) ) {
			exit( '-1<div id="message" class="error bp-ajax-message"><p>' . esc_html__( 'Please do not leave the comment area blank.', 'psourcemediathek' ) . '</p></div>' );
		}

		if ( empty( $_POST['form_id'] ) || empty( $_POST['comment_id'] ) || ! is_numeric( $_POST['form_id'] ) || ! is_numeric( $_POST['comment_id'] ) ) {
			exit( '-1<div id="message" class="error bp-ajax-message"><p>' . esc_html( $feedback ) . '</p></div>' );
		}

		$comment_id = bp_activity_new_comment( array(
			'activity_id' => $_POST['form_id'],
			'content'     => $_POST['content'],
			'parent_id'   => $_POST['comment_id'],
		) );

		if ( ! $comment_id ) {
			if ( ! empty( $bp->activity->errors['new_comment'] ) && is_wp_error( $bp->activity->errors['new_comment'] ) ) {
				$feedback = $bp->activity->errors['new_comment']->get_error_message();
				unset( $bp->activity->errors['new_comment'] );
			}

			exit( '-1<div id="message" class="error bp-ajax-message"><p>' . esc_html( $feedback ) . '</p></div>' );
		}

		// Load the new activity item into the $activities_template global.
		bp_has_activities( 'display_comments=stream&hide_spam=false&show_hidden=true&include=' . $comment_id );

		// Swap the current comment with the activity item we just loaded.
		if ( isset( $activities_template->activities[0] ) ) {
			$activities_template->activity                  = new stdClass();
			$activities_template->activity->id              = $activities_template->activities[0]->item_id;
			$activities_template->activity->current_comment = $activities_template->activities[0];

			// Because the whole tree has not been loaded, we manually
			// determine depth.
			$depth     = 1;
			$parent_id = (int) $activities_template->activities[0]->secondary_item_id;
			while ( $parent_id !== (int) $activities_template->activities[0]->item_id ) {
				$depth ++;
				$p_obj     = new BP_Activity_Activity( $parent_id );
				$parent_id = (int) $p_obj->secondary_item_id;
			}
			$activities_template->activity->current_comment->depth = $depth;
		}

		// get activity comment template part.
		psmt_get_template_part( 'buddypress/activity/comment' );

		unset( $activities_template );
		exit;
	}
}
