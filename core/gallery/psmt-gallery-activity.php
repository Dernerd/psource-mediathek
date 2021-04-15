<?php
// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A wrapper for bp_has_activity
 * checks if the gallery has associated activity
 *
 * @param array $args see args.
 *
 * @return boolean
 */
function psmt_gallery_has_activity( $args = null ) {

	$default = array(
		'gallery_id' => psmt_get_current_gallery_id(),
	);

	$args = wp_parse_args( $args, $default );

	$args = array(
		'meta_query' => array(
			array(
				'key'   => '_psmt_gallery_id',
				'value' => $args['gallery_id'],
			),
			array(
				'key'     => '_psmt_context',
				'value'   => 'gallery',
				'compare' => '=',
			),
		),
		'type'       => 'psmt_media_upload',
		'user_id'    => false,
	);

	return bp_has_activities( $args );
}

/**
 * Delete all activity meta where this gallery is attached
 *
 * @param int $gallery_id gallery id.
 *
 * @return boolean
 */
function psmt_gallery_delete_activity_meta( $gallery_id ) {
	return psmt_delete_activity_meta_by_key_value( '_psmt_gallery_id', $gallery_id );
}

/**
 * Deleets all activity for the gallery
 *
 * @param int $gallery_id gallery id.
 *
 * @return boolean
 */
function psmt_gallery_delete_activity( $gallery_id ) {

	// all activity where meta_key = _psmt_gallery_id.
	return psmt_delete_activity_by_meta_key_value( '_psmt_gallery_id', $gallery_id );
}

/**
 * Get an array of unpublished media ids
 *
 * @param int $gallery_id gallery id.
 *
 * @return array of media ids
 */
function psmt_gallery_get_unpublished_media( $gallery_id ) {
	return psmt_get_gallery_meta( $gallery_id, '_psmt_unpublished_media_id', false );
}

/**
 * Add media to the list of unpublished media
 *
 * @param int       $gallery_id gallery id.
 * @param int|array $media_ids single media id or an array of media ids.
 */
function psmt_gallery_add_unpublished_media( $gallery_id, $media_ids ) {

	if ( ! psourcemediathek()->is_bp_active() ) {
		return;
	}

	$media_ids = (array) $media_ids; // one or more media is given.

	$unpublished = psmt_gallery_get_unpublished_media( $gallery_id );

	$media_ids = array_diff( $media_ids, $unpublished );

	// add all new media ids to the unpublished list.
	foreach ( $media_ids as $new_media_id ) {
		psmt_add_gallery_meta( $gallery_id, '_psmt_unpublished_media_id', $new_media_id );
	}
}

/**
 * Update the list of unpublished media
 *
 * @param int       $gallery_id gallery id.
 * @param int|array $media_ids single media id or an array of media ids.
 */
function psmt_gallery_update_unpublished_media( $gallery_id, $media_ids ) {

	$media_ids = (array) $media_ids; // one or more media is given.

	if ( empty( $media_ids ) ) {
		return;
	}
	// delete all existing media in the list.
	psmt_gallery_delete_unpublished_media( $gallery_id );
	// add the new list.
	psmt_gallery_add_unpublished_media( $gallery_id, $media_ids );
}

/**
 *  Delete the unpublished media
 *
 * @param int       $gallery_id gallery id.
 * @param int|array $media_id either a single media id or an array of media ids.
 */
function psmt_gallery_delete_unpublished_media( $gallery_id, $media_id = array() ) {

	if ( empty( $media_id ) ) {
		// delete all.
		psmt_delete_gallery_meta( $gallery_id, '_psmt_unpublished_media_id' );
	} else {
		// media is given? or media ids are given?
		$media_ids = (array) $media_id;

		foreach ( $media_ids as $mid ) {
			psmt_delete_gallery_meta( $gallery_id, '_psmt_unpublished_media_id', $mid );
		}
	}
}

/**
 * Check if current Gallery has unpublished media
 *
 * @param int $gallery_id gallery id.
 *
 * @return boolean
 */
function psmt_gallery_has_unpublished_media( $gallery_id ) {

	$media_ids = psmt_gallery_get_unpublished_media( $gallery_id );

	if ( ! empty( $media_ids ) ) {
		return true;
	}

	return false;
}

/**
 * Record gallery activity.
 *
 * @param array $args see args.
 *
 * @return bool
 */
function psmt_gallery_record_activity( $args ) {

	// Our activity module is not loaded.
	if ( ! function_exists( 'psmt_record_activity' ) ) {
		return false;
	}

	if ( ! apply_filters( 'psmt_gallery_do_record_activity', true, $args ) ) {
		return false;
	}

	$default = array(
		'id'         => false,
		'gallery_id' => null,
		'media_ids'  => null, // single id or an array of ids.
		'action'     => '',
		'content'    => '',
		'type'       => '', // type of activity  'create_gallery, update_gallery, media_upload etc'.
		//'component'		=> '',// psmt_get_current_component(),
		//'component_id'	=> '',//psmt_get_current_component_id(),
		//'user_id'		=> '',//get_current_user_id(),
	);

	$args = wp_parse_args( $args, $default );

	if ( ! $args['gallery_id'] ) {
		return false;
	}

	$gallery_id = absint( $args['gallery_id'] );

	$gallery = psmt_get_gallery( $gallery_id );

	if ( ! $gallery ) {
		return false;
	}

	$args['status'] = $gallery->status;

	return psmt_record_activity( $args );
}
