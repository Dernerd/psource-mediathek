<?php
/**
 * Various sections for admin enhancement that does not fit at any other place will go here.
 *
 * @package psourcemediathek
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add "View Benutzergalerien" link in the Admin User list
 *
 * @param array   $actions action links.
 * @param WP_User $user current row user.
 *
 * @return array
 */
function psmt_admin_user_row_actions( $actions = array(), $user = null ) {

	if ( empty( $user->ID ) ) {
		return $actions;
	}

	$url = psmt_admin()->get_menu_slug();

	$url = add_query_arg( array(
		'author' => $user->ID,
	), $url );

	$actions['view-psmt-gallery'] = sprintf( '<a href="%s" title="%s">%s</a>', $url, _x( 'Benutzergalerien anzeigen', 'admin user list action link', 'psourcemediathek' ), __( 'Galerien', 'psourcemediathek' ) );

	return $actions;
}
add_filter( 'user_row_actions', 'psmt_admin_user_row_actions', 10, 2 );
