<?php

/**
 * Default menu item visibility check callback
 *
 * @param array       $item menu item.
 * @param PSMT_Gallery $gallery Gallery object.
 *
 * @return boolean
 */
function psmt_is_menu_item_visible( $item, $gallery ) {

	$can_see    = false;
	$user_id    = get_current_user_id();
	$gallery_id = $gallery->id;

	// if the current user is super admin or owner of the gallery, they can see everything.
	if ( is_super_admin() || $user_id == $gallery->user_id ) {
		return apply_filters( 'psmt_is_menu_item_visible', true, $item, $gallery );
	}

	switch ( $item['action'] ) {

		case 'view':
			$can_see = psmt_user_can_list_media( $gallery->id, $user_id );
			break;

		case 'manage':
		case 'edit':
		case 'reorder':
			$can_see = psmt_user_can_edit_gallery( $gallery_id, $user_id );
			break;

		case 'upload':
		case 'add':
			$can_see = psmt_user_can_upload( $gallery->component, $gallery->component_id, $gallery );
			break;

		case 'settings' :
			break;

		case 'delete':
			break;
	}

	/*if ( ! $can_see ) {

		// check if action is protected, If it is not protected, anyone can see.
		if ( ! in_array( $item['action'], array( 'view', 'manage', 'edit', 'reorder', 'upload' ) ) ) {
			$can_see = true;
		}
	}*/

	// should we provide a filter here, I am sure people will misuse it.
	return apply_filters( 'psmt_is_menu_item_visible', $can_see, $item, $gallery );
}

/**
 * Add a new menu item to the current gallery menu
 *
 * @param array $args item args.
 *
 * @return null
 */
function psmt_add_gallery_nav_item( $args ) {
	return psourcemediathek()->get_menu( 'gallery' )->add_item( $args );
}

/**
 * Remove a nav item from the current gallery nav
 *
 * @param string $slug menu slug.
 *
 * @return null
 */
function psmt_remove_gallery_nav_item( $slug ) {
	return psourcemediathek()->get_menu( 'gallery' )->remove_item( $slug );
}

/**
 * Render gallery menu
 *
 * @param PSMT_Gallery $gallery gallery object.
 * @param string      $selected selected menu item.
 */
function psmt_gallery_admin_menu( $gallery, $selected = '' ) {

	$gallery = psmt_get_gallery( $gallery );

	psourcemediathek()->get_menu( 'gallery' )->render( $gallery, $selected );
}

/**
 * Add a new nav item in the media nav
 *
 * @param array $args menu item args.
 *
 * @return boolean
 */
function psmt_add_media_nav_item( $args ) {

	return psourcemediathek()->get_menu( 'media' )->add_item( $args );
}

/**
 * Remove a nav item from the media nav
 *
 * @param array $args array of args.
 *
 * @return null
 */
function psmt_remove_media_nav_item( $args ) {

	return psourcemediathek()->get_menu( 'media' )->remove_item( $args );
}

/**
 * Render media admin tabs
 *
 * @param PSMT_Media $media media object.
 */
function psmt_media_menu( $media, $action = '' ) {

	$media = psmt_get_media( $media );
	psourcemediathek()->get_menu( 'media' )->render( $media );
}
