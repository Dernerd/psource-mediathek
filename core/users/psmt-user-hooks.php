<?php

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Filter current component id for the displayed user profile.
 *
 * @param int $component_id component id.
 *
 * @return int
 */
function psmt_current_component_id_for_user( $component_id ) {

	if ( psourcemediathek()->is_bp_active() && bp_is_user() ) {
		return bp_displayed_user_id(); // that is displayed user id.
	}

	return $component_id;
}
add_filter( 'psmt_get_current_component_id', 'psmt_current_component_id_for_user' );

/**
 * Delete galleries for user on account spam/delete etc.
 *
 * @param int $user_id user id.
 */
function psmt_delete_galleries_for_user( $user_id ) {

	$query = new PSMT_Gallery_Query( array( 'user_id' => $user_id, 'fields' => 'ids' ) );
	$ids   = $query->get_ids();

	// Delete all galleries.
	foreach ( $ids as $gallery_id ) {
		psmt_delete_gallery( $gallery_id );
	}
}
add_action( 'wpmu_delete_user', 'psmt_delete_galleries_for_user', 1 );
add_action( 'delete_user', 'psmt_delete_galleries_for_user', 1 );
add_action( 'make_spam_user', 'psmt_delete_galleries_for_user', 1 );

/**
 * Clear user gallery count meta when a new gallery is created or existing is deleted.
 *
 * @param int|PSMT_Gallery $gallery numeric gallery id or object.
 */
function _psmt_clear_user_gallery_count( $gallery ) {

	$gallery = psmt_get_gallery( $gallery );

	if ( ! $gallery ) {
		$user_id    = get_current_user_id();
		$components = array_keys( psmt_get_registered_components() );
	} else {
		$user_id    = $gallery->user_id;
		$components = $gallery->component;
	}

	$key = 'psmt-u-' . $user_id;

	// all gallery count.
	delete_user_meta( $user_id, $key . '-gallery-count' );

	foreach ( (array) $components as $component ) {
		// component specific gallery count.
		delete_user_meta( $user_id, $key . '-' . $component . '-gallery-count' );
	}
}

add_action( 'psmt_gallery_deleted', '_psmt_clear_user_gallery_count' );
add_action( 'psmt_gallery_created', '_psmt_clear_user_gallery_count' );


/**
 * Clear user media count meta when a new media is uploaded or existing is deleted.
 *
 * @param int|PSMT_Media $media numeric media id or object.
 */
function _psmt_clear_user_media_count( $media ) {

	$media = psmt_get_media( $media );

	if ( ! $media ) {
		$user_id    = get_current_user_id();
		$components = array_keys( psmt_get_registered_components() );
	} else {
		$user_id    = $media->user_id;
		$components = $media->component;
	}

	$key = 'psmt-u-' . $user_id;

	// all media count.
	delete_user_meta( $user_id, $key . '-media-count' );

	foreach ( (array) $components as $component ) {
		// component specific media count.
		delete_user_meta( $user_id, $key . '-' . $component . '-media-count' );
	}
}

add_action( 'psmt_media_deleted', '_psmt_clear_user_media_count' );
add_action( 'psmt_media_added', '_psmt_clear_user_media_count' );
