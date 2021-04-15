<?php
/**
 * Media activity.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A wrapper for bp_has_activity
 * Check if the activities for a media exist
 *
 * @param array $args bp_has_activity args.
 *
 * @return bool
 */
function psmt_media_has_activity( $args = null ) {

	$default = array(
		'media_id' => psmt_get_current_media_id(),
	);

	$args = wp_parse_args( $args, $default );

	$args = array(
		'meta_query' => array(
			array(
				'key'   => '_psmt_media_id',
				'value' => $args['media_id'],
			),
		),
		'type'       => 'psmt_media_upload',
		'user_id'    => false,
	);

	return bp_has_activities( $args );
}

/**
 * Delete all the metas where the key and value matches given pair
 *
 * @param int $media_id media id.
 *
 * @return bool
 */
function psmt_media_delete_attached_activity_media_id( $media_id ) {
	return psmt_delete_activity_meta_by_key_value( '_psmt_attached_media_id', $media_id );
}

/**
 * Get associated activity Id for Media
 *
 * @param int $media_id media id.
 *
 * @return int
 */
function psmt_media_get_activity_id( $media_id ) {
	return psmt_get_media_meta( $media_id, '_psmt_activity_id', true );
}

/**
 * Update associated activity id.
 *
 * @param int $media_id media id.
 * @param int $activity_id activity id.
 *
 * @return bool|int
 */
function psmt_media_update_activity_id( $media_id, $activity_id ) {
	return psmt_update_media_meta( $media_id, '_psmt_activity_id', $activity_id );
}

/**
 * Check if Media has an activity associated
 *
 * @param int $media_id media id.
 *
 * @return bool
 */
function psmt_media_has_activity_entries( $media_id ) {
	return psmt_media_get_activity_id( $media_id );
}

/**
 * Delete all activity comments for this media
 *
 * @param int $media_id media id.
 *
 * @return bool
 */
function psmt_media_delete_activities( $media_id ) {
	return psmt_delete_activity_by_meta_key_value( '_psmt_media_id', $media_id );
}


/**
 * Delete all activity meta entry for this media.
 * always call after deleting the media.
 *
 * @param int $media_id media id.
 */
function psmt_media_delete_activity_meta( $media_id ) {
	// delete _psmt_media_id
	// psmt_delete_activity_meta_by_key_value( '_psmt_media_id', $media_id );
	// delete _psmt_attached_media_ids.
	psmt_delete_activity_meta_by_key_value( '_psmt_attached_media_id', $media_id );
}
