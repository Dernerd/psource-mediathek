<?php
/**
 * Group Gallery actions.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delete Galleries of the group when a group is deleted
 *
 * @param int $group_id group id.
 */
function psmt_delete_galleries_for_group( $group_id ) {

	// Get Alle Galerien for this group.
	$query = new PSMT_Gallery_Query( array( 'component_id' => $group_id, 'fields' => 'ids', 'component' => 'groups' ) );
	$ids   = $query->get_ids();

	// Delete all galleries.
	foreach ( $ids as $gallery_id ) {
		psmt_delete_gallery( $gallery_id );
	}
}

add_action( 'groups_delete_group', 'psmt_delete_galleries_for_group' ); // group id.
