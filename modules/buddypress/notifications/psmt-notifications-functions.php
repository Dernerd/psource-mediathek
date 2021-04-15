<?php
/**
 * Notifications
 *
 * @package    PsourceMediathek
 * @subpackage modules/buddypress
 * @copyright  Copyright (c) 2018, DerN3rd
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     DerN3rd
 * @since      1.0.0
 */
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add notification.
 *
 * @param int    $user_id user id.
 * @param string $action_type action.
 * @param int    $psmt_id media or gallery id.
 * @param int    $other_user_id other user id who commented.
 *
 * @return bool|int
 */
function psmt_send_bp_notification( $user_id, $action_type, $psmt_id, $other_user_id ) {

	if ( ! $user_id || ! $action_type || ! $psmt_id ) {
		return false;
	}

	if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'notifications' ) ) {
		return false;
	}

	return bp_notifications_add_notification( array(
		'user_id'           => $user_id,
		'item_id'           => $psmt_id,
		'secondary_item_id' => $other_user_id,
		'component_name'    => 'psourcemediathek',
		'component_action'  => $action_type,
	) );
}

/**
 * Notification formatting callback for PsourceMediathek.
 *
 * @param string $action Action type.
 * @param int    $item_id media or gallery id.
 * @param int    $secondary_item_id The secondary item ID.
 * @param int    $total_items The total number of notifications.
 * @param string $format 'string' or 'array'.
 *
 * @return array|string
 */
function psmt_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

	$link = '';
	switch ( $action ) {
		case 'psmt_media_comment':
			$media = psmt_get_media( $item_id );
			$link  = trailingslashit( psmt_get_media_permalink( $media ) );

			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( '%1$d people commented on your %2$s', 'psourcemediathek' ), (int) $total_items, strtolower( psmt_get_type_singular_name( $media->type ) ) );
			} else {
				$text = sprintf( __( '%1$s commented on your %2$s', 'psourcemediathek' ), bp_core_get_user_displayname( $secondary_item_id ), strtolower( psmt_get_type_singular_name( $media->type ) ) );
			}

			break;

		case 'psmt_gallery_comment':
			$gallery = psmt_get_gallery( $item_id );
			$link    = psmt_get_gallery_permalink( $gallery );

			// Set up the string and the filter.
			if ( (int) $total_items > 1 ) {
				$text = sprintf( __( '%d people commented on your gallery', 'psourcemediathek' ), (int) $total_items );
			} else {
				$text = sprintf( __( '%s commented on your gallery', 'psourcemediathek' ), bp_core_get_user_displayname( $secondary_item_id ) );
			}

			break;
	}

	// Return either an HTML link or an array, depending on the requested format.
	if ( 'string' == $format ) {
		$return = '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
	} else {
		/** This filter is documented in bp-friends/bp-friends-notifications.php */
		$return = array(
			'link' => $link,
			'text' => $text,
		);
	}

	do_action( 'psmt_format_notifications', $action, $item_id, $secondary_item_id, $total_items, $return );

	return $return;
}

