<?php
/**
 * Media action handling.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Single media Edit details
 *
 * @return null
 */
function psmt_action_edit_media() {

	// allow media to be edited from anywhere.
	if ( empty( $_POST['psmt-action'] ) || $_POST['psmt-action'] != 'edit-media' ) {
		return;
	}

	$referrer = wp_get_referer();

	// if we are here, It is media edit action.
	if ( ! wp_verify_nonce( $_POST['psmt-nonce'], 'psmt-edit-media' ) ) {
		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	$media_id = absint( $_POST['psmt-media-id'] );

	if ( ! $media_id ) {
		return;
	}

	// check for permission.
	if ( ! psmt_user_can_edit_media( $media_id ) ) {
		psmt_add_feedback( __( "Du hast keine Berechtigung, dies zu bearbeiten!", 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// if we are here, validate the data and let us see if we can update.
	$title       = $_POST['psmt-media-title'];
	$description = $_POST['psmt-media-description'];

	$status = $_POST['psmt-media-status'];
	$errors = array();
	// @todo
	// In future, replace with media type functions.
	if ( ! psmt_is_active_status( $status ) ) {
		$errors['status'] = __( 'Ungültiger Medienstatus!', 'psourcemediathek' );
	}

	if ( empty( $title ) ) {
		$errors['title'] = __( 'Titel darf nicht leer sein', 'psourcemediathek' );
	}

	// give opportunity to other plugins to add their own validation errors.
	$validation_errors = apply_filters( 'psmt-edit-media-field-validation', $errors, $_POST );

	if ( ! empty( $validation_errors ) ) {
		// let us add the validation error and return back to the earlier page.
		$message = join( '\r\n', $validation_errors );

		psmt_add_feedback( $message, 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// let us update media.
	$media_id = psmt_update_media( array(
		'title'       => $title,
		'description' => $description,
		'status'      => $status,
		'creator_id'  => get_current_user_id(),
		'id'          => $media_id,
	) );


	if ( ! $media_id ) {
		psmt_add_feedback( __( 'Aktualisierung nicht möglich!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// if we are here, the gallery was created successfully,
	// let us redirect to the gallery_slug/manage/upload page.
	$redirect_url = psmt_get_media_edit_url( $media_id );

	psmt_add_feedback( __( 'Erfolgreich aktualisiert!', 'psourcemediathek' ) );

	psmt_redirect( $redirect_url );
}

add_action( 'psmt_actions', 'psmt_action_edit_media', 2 );

/**
 * Handles Media deletion
 *
 * @return null
 */
function psmt_action_delete_media() {


	if ( empty( $_REQUEST['psmt-action'] ) || $_REQUEST['psmt-action'] != 'delete-media' ) {
		return;
	}

	if ( ! $_REQUEST['psmt-media-id'] ) {
		return;
	}

	$referrer = wp_get_referer();

	if ( ! wp_verify_nonce( $_REQUEST['psmt-nonce'], 'psmt-delete-media' ) ) {
		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	$media = '';

	if ( ! empty( $_REQUEST['psmt-media-id'] ) ) {
		$media = psmt_get_media( (int) $_REQUEST['psmt-media-id'] );
	}

	// check for permission
	// we may want to allow passing of component from the form in future!
	if ( ! psmt_user_can_delete_media( $media->id ) ) {
		psmt_add_feedback( __( "Du hast keine Erlaubnis, dies zu löschen!", 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// if we are here, delete media and redirect to the component base url.
	psmt_delete_media( $media->id );

	$redirect_url = apply_filters( 'psmt_media_delete_redirect_url', psmt_get_gallery_permalink( $media->gallery_id ), $media );

	psmt_add_feedback( __( 'Erfolgreich gelöscht!', 'psourcemediathek' ), 'error' );

	psmt_redirect( $redirect_url );
}

add_action( 'psmt_actions', 'psmt_action_delete_media', 2 );

/**
 * Handles Media Cover deletion
 */
function psmt_action_delete_media_cover() {

	if ( ! psmt_is_media_management() ) {
		return;
	}

	if ( ! isset( $_REQUEST['psmt-action'] ) || ( $_REQUEST['psmt-action'] != 'cover-delete' ) || empty( $_REQUEST['media_id'] ) ) {
		return;
	}

	$media = psmt_get_media( absint( $_REQUEST['media_id'] ) );

	if ( empty( $media ) ) {
		return;
	}

	$referrer = $redirect_url = psmt_get_media_edit_url( $media );

	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'cover-delete' ) ) {
		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}


	// we may want to allow passing of component from the form in future!
	if ( ! psmt_user_can_delete_media( $media ) ) {
		psmt_add_feedback( __( "Du hast keine Erlaubnis, dieses Cover zu löschen!", 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}
	// we always need to delete this.
	$cover_id = psmt_get_media_cover_id( $media->id );
	psmt_delete_media_cover_id( $media->id );

	psmt_delete_media( $cover_id );

	psmt_add_feedback( __( 'Cover erfolgreich gelöscht!', 'psourcemediathek' ) );

	// if we are here, delete gallery and redirect to the component base url.
	psmt_redirect( $redirect_url );
}

add_action( 'psmt_actions', 'psmt_action_delete_media_cover', 2 );

/**
 * Record a new upload activity if auto publishing is enabled in the
 *
 * @param int $media_id media id.
 */
function psmt_action_record_new_media_activity( $media_id ) {

	if ( ! psmt_is_auto_publish_to_activity_enabled( 'add_media' ) || apply_filters( 'psmt_do_not_record_add_media_activity', false ) ) {
		return;
	}

	$media = psmt_get_media( $media_id );

	// if media is upload from activity, do not publish it again to activity.
	if ( $media->context == 'activity' ) {
		return;
	}

	$user_link = psmt_get_user_link( $media->user_id );

	$link = psmt_get_media_permalink( $media );

	psmt_media_record_activity( array(
		'media_id' => $media_id,
		'type'     => 'add_media',
		'content'  => '',
		'action'   => sprintf( __( '%s hat <a href="%s">%s</a> hinzugefügt ', 'psourcemediathek' ), $user_link, $link, strtolower( psmt_get_type_singular_name( $media->type ) ) ),
	) );
}

add_action( 'psmt_media_added', 'psmt_action_record_new_media_activity' );

/**
 * Cleanup cache when media is updated or deleted
 *
 * @param PSMT_Media $media media object.
 */
function psmt_clean_media_cache( $media ) {

	if ( is_object( $media ) && is_a( $media, 'PSMT_Media' ) ) {
		$media = $media->id;
	}

	psmt_delete_media_cache( $media );
}

// Clear cache on media delete.
add_action( 'psmt_media_deleted', 'psmt_clean_media_cache', 100 );
// Clear cache on Media update.
add_action( 'psmt_media_updated', 'psmt_clean_media_cache', 100 );
