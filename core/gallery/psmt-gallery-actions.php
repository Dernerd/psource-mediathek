<?php

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Various Gallery related actions handlers
 */

/**
 * Handles Gallery creation on the front end in non ajax case
 */
function psmt_action_create_gallery() {

	// allow gallery to be created from anywhere.
	// the form must have psmt-action set and It should be set to 'create-gallery'.
	if ( empty( $_POST['psmt-action'] ) || 'create-gallery' !== $_POST['psmt-action']  ) {
		return;
	}

	$referrer = wp_get_referer();
	// if we are here, It is gallery create action.
	if ( ! wp_verify_nonce( $_POST['psmt-nonce'], 'psmt-create-gallery' ) ) {
		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}
	// update it to allow passing component/id from the form.
	$component    = isset( $_POST['psmt-gallery-component'] ) ? $_POST['psmt-gallery-component'] : psmt_get_current_component();
	$component_id = isset( $_POST['psmt-gallery-component-id'] ) ? $_POST['psmt-gallery-component-id'] : psmt_get_current_component_id();

	// check for permission
	// we may want to allow passing of component from the form in future!
	if ( ! psmt_user_can_create_gallery( $component, $component_id ) ) {
		psmt_add_feedback( __( "Du hast keine Berechtigung zum Erstellen einer Galerie!", 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	$title       = $_POST['psmt-gallery-title'];
	$description = $_POST['psmt-gallery-description'];

	$type   = $_POST['psmt-gallery-type'];
	$status = $_POST['psmt-gallery-status'];
	$errors = array();

	// if we are here, validate the data and let us see if we can create.
	if ( ! psmt_is_active_status( $status ) ) {
		$errors['status'] = __( 'Ungültiger Galeriestatus!', 'psourcemediathek' );
	}

	if ( ! psmt_is_active_type( $type ) ) {
		$errors['type'] = __( 'Ungültiger Galerietyp!', 'psourcemediathek' );
	}

	// check for current component.
	if ( ! psmt_is_enabled( $component, $component_id ) ) {
		$errors['component'] = __( 'Ungültige Aktion!', 'psourcemediathek' );
	}

	if ( empty( $title ) ) {
		$errors['title'] = __( 'Titel darf nicht leer sein', 'psourcemediathek' );
	}

	// Give opportunity to other plugins to add their own validation errors.
	$validation_errors = apply_filters( 'psmt-create-gallery-field-validation', $errors, $_POST );

	if ( ! empty( $validation_errors ) ) {
		// let us add the validation error and return back to the earlier page.
		$message = join( '\r\n', $validation_errors );

		psmt_add_feedback( $message, 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// let us create gallery.
	$gallery_id = psmt_create_gallery( array(
		'title'        => $title,
		'description'  => $description,
		'type'         => $type,
		'status'       => $status,
		'creator_id'   => get_current_user_id(),
		'component'    => $component,
		'component_id' => $component_id,
	) );

	if ( ! $gallery_id ) {
		psmt_add_feedback( __( 'Galerie kann nicht erstellt werden!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// if we are here, the gallery was created successfully,
	// let us redirect to the gallery_slug/manage/upload page.
	$redirect_url = psmt_get_gallery_add_media_url( $gallery_id );

	psmt_add_feedback( __( 'Galerie erfolgreich erstellt!', 'psourcemediathek' ) );

	psmt_redirect( $redirect_url );
}
add_action( 'psmt_actions', 'psmt_action_create_gallery', 2 );

/**
 * Handles gallery details updation
 */
function psmt_action_edit_gallery() {
	// allow gallery to be edited from anywhere.
	if ( empty( $_POST['psmt-action'] ) || $_POST['psmt-action'] != 'edit-gallery' ) {
		return;
	}

	$referrer = wp_get_referer();

	// if we are here, It is gallery edit action.
	if ( ! wp_verify_nonce( $_POST['psmt-nonce'], 'psmt-edit-gallery' ) ) {
		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	$gallery_id = absint( $_POST['psmt-gallery-id'] );

	$gallery = psmt_get_gallery( $gallery_id );

	if ( ! $gallery_id || empty( $gallery ) ) {
		return;
	}

	// check for permission
	// we may want to allow passing of component from the form in future!
	if ( ! psmt_user_can_edit_gallery( $gallery_id ) ) {
		psmt_add_feedback( __( "Du hast keine Berechtigung, diese Galerie zu bearbeiten!", 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// if we are here, validate the data and let us see if we can update.
	$title       = $_POST['psmt-gallery-title'];
	$description = $_POST['psmt-gallery-description'];


	$status = $_POST['psmt-gallery-status'];
	$errors = array();

	if ( ! psmt_is_active_status( $status ) ) {
		$errors['status'] = __( 'Ungültiger Galeriestatus!', 'psourcemediathek' );
	}

	if ( empty( $title ) ) {
		$errors['title'] = __( 'Titel darf nicht leer sein', 'psourcemediathek' );
	}

	// Give opportunity to other plugins to add their own validation errors.
	$validation_errors = apply_filters( 'psmt-edit-gallery-field-validation', $errors, $_POST );

	if ( ! empty( $validation_errors ) ) {
		//let us add the validation error and return back to the earlier page
		$message = join( '\r\n', $validation_errors );

		psmt_add_feedback( $message, 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// let us create gallery.
	$gallery_id = psmt_update_gallery( array(
		'title'       => $title,
		'description' => $description,
		'status'      => $status,
		//'creator_id'	=> $gallery->user_id,
		'id'          => $gallery_id,
	) );


	if ( ! $gallery_id ) {
		psmt_add_feedback( __( 'Galerie kann nicht aktualisiert werden!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// if we are here, the gallery was created successfully,
	// let us redirect to the gallery_slug/manage/upload page.
	$redirect_url = psmt_get_gallery_settings_url( $gallery_id );

	psmt_add_feedback( __( 'Galerie erfolgreich aktualisiert!', 'psourcemediathek' ) );

	psmt_redirect( $redirect_url );
}
add_action( 'psmt_actions', 'psmt_action_edit_gallery', 2 ); //update gallery settings, cover

/**
 * Handles Gallery deletion
 */
function psmt_action_delete_gallery() {

	if ( empty( $_POST['psmt-action'] ) || $_POST['psmt-action'] != 'delete-gallery' ) {
		return;
	}

	if ( empty( $_POST['gallery_id'] ) ) {
		return;
	}

	$referrer = wp_get_referer();

	if ( ! wp_verify_nonce( $_POST['psmt-nonce'], 'psmt-delete-gallery' ) ) {
		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	if ( empty( $_POST['psmt-delete-agree'] ) ) {
		return;
		//@todo add feedback that you must agree
	}

	$gallery = '';

	if ( ! empty( $_POST['gallery_id'] ) ) {
		$gallery = psmt_get_gallery( absint( $_POST['gallery_id'] ) );
	}

	// check for permission
	// we may want to allow passing of component from the form in future!
	if ( ! psmt_user_can_delete_gallery( $gallery ) ) {
		psmt_add_feedback( __( "Du hast keine Erlaubnis, diese Galerie zu löschen!", 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	// if we are here, delete gallery and redirect to the component base url.
	$redirect_url = apply_filters( 'psmt_delete_gallery_redirect_location', psmt_get_gallery_base_url( $gallery->component, $gallery->component_id ), $gallery );

	psmt_delete_gallery( $gallery->id );

	psmt_redirect( $redirect_url );
}
add_action( 'psmt_actions', 'psmt_action_delete_gallery', 2 );

/**
 * Handles Gallery bulk edit action
 */
function psmt_action_gallery_media_bulkedit() {

	if ( empty( $_POST['psmt-action'] ) || $_POST['psmt-action'] != 'edit-gallery-media' ) {
		return;
	}

	if ( ! $_POST['psmt-editing-media-ids'] ) {
		return;
	}

	$referrer = wp_get_referer();

	if ( ! wp_verify_nonce( $_POST['psmt-nonce'], 'psmt-edit-gallery-media' ) ) {

		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}

	$media_ids = $_POST['psmt-editing-media-ids'];
	$media_ids = wp_parse_id_list( $media_ids );


	$bulk_action = false;

	if ( ! empty( $_POST['psmt-edit-media-bulk-action'] ) ) {
		$bulk_action = $_POST['psmt-edit-media-bulk-action']; //we are leaving this to allow future enhancements with other bulk action and not restricting to delete only
	}

	$user_id = get_current_user_id();

	foreach ( $media_ids as $media_id ) {
		// check what action should we take?
		// 1. check if $bulk_action is set? then we may ned to check for deletion
		// otherwise, just update the details :)
		if ( $bulk_action == 'delete' && ! empty( $_POST['psmt-delete-media-check'][ $media_id ] ) ) {

			// delete and continue
			// check if current user can delete?
			if ( ! psmt_user_can_delete_media( $media_id ) ) {
				// if the user is unable to delete media, should we just continue the loop or breakout and redirect back with error?
				// I am in favour of showing error.
				psmt_add_feedback( __( 'Darf nicht löschen!', 'psourcemediathek' ), 'error' );

				if ( $referrer ) {
					psmt_redirect( $referrer );
				}

				return;
			}

			// if we are here, let us delete the media.
			psmt_delete_media( $media_id );

			psmt_add_feedback( __( 'Erfolgreich gelöscht!', 'psourcemediathek' ), 'error' ); //it will do for each media, that is not  good thing btw

			continue;
		}
		// since we already handled delete for the media checked above,
		// we don't want to do it for the other media hoping that the user was performing bulk delete and not updating the media info.
		if ( $bulk_action == 'delete' ) {
			continue;
		}

		if ( ! psmt_user_can_edit_media( $media_id, $user_id ) ) {
			// if the user is unable to edit media, should we just continue the loop or breakout and redirect back with error?
			// I am in favour of showing error.
			psmt_add_feedback( __( 'Nicht aktualisieren dürfen!', 'psourcemediathek' ), 'error' );

			if ( $referrer ) {
				psmt_redirect( $referrer );
			}

			return;
		}

		$media_title = $_POST['psmt-media-title'][ $media_id ];

		$media_description = $_POST['psmt-media-description'][ $media_id ];

		$status = $_POST['psmt-media-status'][ $media_id ];
		// type is not editable
		// $type = $_POST['psmt-media-type'][$media_id];
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
		psmt_add_feedback( __( 'Aktualisiert!', 'psourcemediathek' ) );
	}

	if ( $referrer ) {
		psmt_redirect( $referrer );
	}
}
add_action( 'psmt_actions', 'psmt_action_gallery_media_bulkedit', 2 );

/**
 * Handles Gallery Media Reordering
 */
function psmt_action_reorder_gallery_media() {

	if ( empty( $_POST['psmt-action'] ) || $_POST['psmt-action'] != 'reorder-gallery-media' ) {
		return;
	}

	$referrer = wp_get_referer();

	if ( ! wp_verify_nonce( $_POST['psmt-nonce'], 'psmt-reorder-gallery-media' ) ) {
		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		psmt_redirect( $referrer );
	}
	// should we check for the permission? not here.
	$media_ids = $_POST['psmt-media-ids']; //array

	$media_ids = wp_parse_id_list( $media_ids );

	$order = count( $media_ids );

	foreach ( $media_ids as $media_id ) {

		if ( ! psmt_user_can_edit_media( $media_id ) ) {
			// unauthorized attempt.
			psmt_add_feedback( __( "Du hast keine Berechtigung zum Aktualisieren!", 'psourcemediathek' ), 'error' );

			if ( $referrer ) {
				psmt_redirect( $referrer );
			}

			return;
		}

		// if we are here, let us update the order.
		psmt_update_media_order( $media_id, $order );
		$order --;
	}

	if ( isset( $media_id) && $media_id ) {
		// mark the gallery assorted, we use it in PSMT_Media_Query to see what should be the default order.
		$media = psmt_get_media( $media_id );
		// mark the gallery as sorted.
		psmt_mark_gallery_sorted( $media->gallery_id );
	}

	psmt_add_feedback( __( 'Aktualisiert', 'psourcemediathek' ) );

	if ( $referrer ) {
		psmt_redirect( $referrer );
	}
}
add_action( 'psmt_actions', 'psmt_action_reorder_gallery_media', 2 );

/**
 * Handles Gallery Cover deletion
 */
function psmt_action_delete_gallery_cover() {

	if ( ! psmt_is_gallery_cover_delete() ) {
		return;
	}

	if ( ! $_REQUEST['gallery_id'] ) {
		return;
	}

	$gallery = psmt_get_gallery( absint( $_REQUEST['gallery_id'] ) );

	$referrer = $redirect_url = psmt_get_gallery_settings_url( $gallery );

	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'delete-cover' ) ) {
		// add error message and return back to the old page.
		psmt_add_feedback( __( 'Aktion nicht autorisiert!', 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}


	// we may want to allow passing of component from the form in future!
	if ( ! psmt_user_can_delete_gallery( $gallery ) ) {

		psmt_add_feedback( __( "Du hast keine Erlaubnis, dieses Cover zu löschen!", 'psourcemediathek' ), 'error' );

		if ( $referrer ) {
			psmt_redirect( $referrer );
		}

		return;
	}
	// we always need to delete this.
	$cover_id = psmt_get_gallery_cover_id( $gallery->id );
	psmt_delete_gallery_cover_id( $gallery->id );

	// if( $gallery->type != 'photo' ) {
	// delete the uploaded cover too
	psmt_delete_media( $cover_id );

	//}
	psmt_add_feedback( __( 'Cover erfolgreich gelöscht!', 'psourcemediathek' ) );

	//if we are here, delete gallery and redirect to the component base url
	psmt_redirect( $redirect_url );
}
add_action( 'psmt_actions', 'psmt_action_delete_gallery_cover', 2 );

function psmt_action_publish_gallery_media_to_activity() {

	if ( ! psourcemediathek()->is_bp_active() || ! is_user_logged_in() || ! psmt_is_gallery_management() || ! bp_is_action_variable( 'publish', 1 ) ) {
		return;
	}

	$gallery_id = absint( $_GET['gallery_id'] );

	if ( ! $gallery_id ) {
		return;
	}

	$referrer = psmt_get_gallery_edit_media_url( $gallery_id );

	// verify nonce.
	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'publish' ) ) {
		psmt_add_feedback( __( 'Nicht autorisierte Aktion.', 'psourcemediathek' ), 'error' );
		psmt_redirect( $referrer );
	}

	// all is good check for permission.
	if ( ! psmt_user_can_publish_gallery_activity( $gallery_id ) ) {

		psmt_add_feedback( __( "Du hast keine ausreichende Erlaubnis.", 'psourcemediathek' ), 'error' );
		psmt_redirect( $referrer );
	}

	if ( ! psmt_gallery_has_unpublished_media( $gallery_id ) ) {

		psmt_add_feedback( __( 'Nichts zu veröffentlichen.', 'psourcemediathek' ), 'error' );
		psmt_redirect( $referrer );
	}

	// now we can safely publish.
	$media_ids = psmt_gallery_get_unpublished_media( $gallery_id );

	$media_count = count( $media_ids );

	$gallery = psmt_get_gallery( $gallery_id );

	$type = $gallery->type;

	$type_name = _n( $type, $type . 's', $media_count );
	$user_link = psmt_get_user_link( get_current_user_id() );

	$gallery_url = psmt_get_gallery_permalink( $gallery );

	$gallery_link = '<a href="' . esc_url( $gallery_url ) . '" title="' . esc_attr( $gallery->title ) . '">' . psmt_get_gallery_title( $gallery ) . '</a>';
	//has media, has permission, so just publish now.
	$activity_id = psmt_gallery_record_activity( array(
		'gallery_id' => $gallery_id,
		'media_ids'  => $media_ids,
		'type'       => 'media_publish',
		'action'     => sprintf( __( '%s teilt %d %s mit %s ', 'mediaprses' ), $user_link, $media_count, $type_name, $gallery_link ),
		'content'    => '',
	) );


	if ( $activity_id ) {
		psmt_gallery_delete_unpublished_media( $gallery_id );

		psmt_add_feedback( __( "Erfolgreich für Aktivität veröffentlicht.", 'psourcemediathek' ) );
	} else {
		psmt_add_feedback( __( "Es gab ein Problem. Bitte versuche es später noch einmal.", 'psourcemediathek' ), 'error' );
	}

	psmt_redirect( $referrer );
}
add_action( 'psmt_actions', 'psmt_action_publish_gallery_media_to_activity', 2 );

function psmt_action_hide_unpublished_media() {

	if ( ! psourcemediathek()->is_bp_active() || ! is_user_logged_in() || ! psmt_is_gallery_management() || ! bp_is_action_variable( 'delete-unpublished', 1 ) ) {
		return;
	}

	$gallery_id = absint( $_GET['gallery_id'] );

	if ( ! $gallery_id ) {
		return;
	}
	// verify nonce.
	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'delete-unpublished' ) ) {
		// should we return or show error?
		return;
	}

	$referrer = psmt_get_gallery_edit_media_url( $gallery_id );

	if ( ! psmt_gallery_has_unpublished_media( $gallery_id ) ) {
		psmt_add_feedback( __( 'Nichts zu verstecken.', 'psourcemediathek' ), 'error' );
		psmt_redirect( $referrer );
	}

	// check if user has permission?
	if ( ! psmt_user_can_publish_gallery_activity( $gallery_id ) ) {
		psmt_add_feedback( __( "Du hast keine ausreichende Erlaubnis.", 'psourcemediathek' ), 'error' );
		psmt_redirect( $referrer );
	}

	psmt_gallery_delete_unpublished_media( $gallery_id );

	psmt_add_feedback( __( "Erfolgreich versteckt!", 'psourcemediathek' ) );

	psmt_redirect( $referrer );
}
add_action( 'psmt_actions', 'psmt_action_hide_unpublished_media', 2 );

/**
 * When a gallery post type post is created from the dashboard, we force to make it look like the one created from front end
 *
 * @param int $post_id numeric post id.
 * @param WP_Post $post post object.
 * @param string $update what type of post action it is.
 */

/** Cleanup actions */
/**
 * Clear cache on gallery update/delete.
 *
 * @param int|PSMT_Gallery $gallery gallery id or object.
 */
function psmt_clean_gallery_cache( $gallery ) {

	if ( is_object( $gallery ) && is_a( $gallery, 'PSMT_Gallery' ) ) {
		$gallery = $gallery->id;
	}

	psmt_delete_gallery_cache( $gallery );

}
add_action( 'psmt_gallery_deleted', 'psmt_clean_gallery_cache', 100 );
// Delete gallery from cache when gallery is updated.
add_action( 'psmt_gallery_updated', 'psmt_clean_gallery_cache', 100 );

/**
 * Generate an activity when a new gallery is created
 * We will not generate any activity for profile galleries though.
 *
 * @param int $gallery_id numeric gallery id.
 */
function psmt_action_new_gallery_activity( $gallery_id ) {

	// if the admin settings does not ask us to create new activity,
	// Or it is explicitly restricted, do not proceed.
	if ( ! psmt_is_auto_publish_to_activity_enabled( 'create_gallery' ) || apply_filters( 'psmt_do_not_record_create_gallery_activity', false ) ) {
		return;
	}

	$gallery   = psmt_get_gallery( $gallery_id );
	$user_link = psmt_get_user_link( $gallery->user_id );

	$link = psmt_get_gallery_permalink( $gallery );
	// record gallery creation as activity, there will be issue with wall gallery though.
	psmt_gallery_record_activity( array(
		'gallery_id' => $gallery_id,
		'type'       => 'create_gallery',
		'content'    => '',
		'action'     => sprintf( __( '%s erstellte eine %s <a href="%s">Galerie</a>', 'psourcemediathek' ), $user_link, strtolower( psmt_get_type_singular_name( $gallery->type ) ), $link ),
	) );
}
add_action( 'psmt_gallery_created', 'psmt_action_new_gallery_activity' );

/**
 * Cleanup count in user meta.
 *
 * @param int $gallery_id gallery id.
 */
function _psmt_gallery_delete_user_gallery_count_meta( $gallery_id ) {
	$gallery = psmt_get_gallery( $gallery_id );
	if ( ! $gallery ) {
		return;
	}
	$user_id = $gallery->user_id;
	$blog_id = get_current_blog_id();
	delete_user_meta( $user_id, '_psmt_gallery_count_members_' . $blog_id . '_nonlogged' );
	delete_user_meta( $user_id, '_psmt_gallery_count_members_' . $blog_id . '_self' );
}

add_action( 'psmt_gallery_created', '_psmt_gallery_delete_user_gallery_count_meta' );
add_action( 'psmt_gallery_updated', '_psmt_gallery_delete_user_gallery_count_meta' );
add_action( 'psmt_before_gallery_delete', '_psmt_gallery_delete_user_gallery_count_meta' );
